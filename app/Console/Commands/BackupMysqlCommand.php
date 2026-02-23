<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;

class BackupMysqlCommand extends Command
{
    protected $signature = 'backup:mysql';

    protected $description = 'Dump MySQL database to gzipped SQL and upload to DO Spaces (hourly; weekly/monthly when due), with retention.';

    private const HOURLY_KEEP = 24;
    private const WEEKLY_KEEP = 4;
    private const MONTHLY_KEEP = 12;

    private const BACKUPS_PREFIX = 'backups';

    public function handle(): int
    {
        $db = config('database.connections.mysql');
        $host = $db['host'] ?? '127.0.0.1';
        $port = $db['port'] ?? 3306;
        $database = $db['database'] ?? '';
        $username = $db['username'] ?? '';
        $password = $db['password'] ?? '';

        if ($database === '' || $username === '') {
            $this->error('Database or username not configured.');

            return self::FAILURE;
        }

        $disk = Storage::disk('spaces');
        if (! config('filesystems.disks.spaces.bucket')) {
            $this->error('Spaces bucket not configured.');

            return self::FAILURE;
        }

        $tmpFile = tempnam(sys_get_temp_dir(), 'mysql_backup_');
        if ($tmpFile === false) {
            $this->error('Could not create temp file.');

            return self::FAILURE;
        }

        $tmpDir = dirname($tmpFile);
        $myCnf = $tmpDir . '/.my.cnf.' . uniqid('', true);
        $myCnfContent = "[client]\n"
            . "user=" . addcslashes($username, "\\\n\r\t") . "\n"
            . "password=" . addcslashes($password, "\\\n\r\t") . "\n"
            . "host=" . addcslashes($host, "\\\n\r\t") . "\n"
            . "port=" . (int) $port . "\n";

        if (file_put_contents($myCnf, $myCnfContent, LOCK_EX) === false) {
            $this->error('Could not write temporary MySQL config.');

            return self::FAILURE;
        }

        @chmod($myCnf, 0600);

        try {
            $env = array_merge($_ENV, [
                'MYSQL_HOME' => $tmpDir,
            ]);

            $mysqldump = Process::run([
                'mysqldump',
                '--defaults-extra-file=' . $myCnf,
                '--single-transaction',
                '--quick',
                '--lock-tables=false',
                $database,
            ], null, $env)->throw();

            if (! $mysqldump->successful()) {
                $this->error('mysqldump failed: ' . $mysqldump->errorOutput());

                return self::FAILURE;
            }

            $gz = gzopen($tmpFile . '.gz', 'wb9');
            if ($gz === false) {
                $this->error('Could not open gzip stream.');

                return self::FAILURE;
            }
            gzwrite($gz, $mysqldump->output());
            gzclose($gz);
            $mysqldump = null;

            $gzContent = file_get_contents($tmpFile . '.gz');
            $now = now();
            $suffix = $now->format('dmY-His') . '.sql.gz';

            $hourlyPath = self::BACKUPS_PREFIX . '/hourly/hourly_' . $suffix;
            if (! $disk->put($hourlyPath, $gzContent)) {
                $this->error('Failed to upload hourly backup.');

                return self::FAILURE;
            }

            $this->prunePrefix($disk, self::BACKUPS_PREFIX . '/hourly/', self::HOURLY_KEEP);

            if ($this->isWeeklyRun($now)) {
                $weeklyPath = self::BACKUPS_PREFIX . '/weekly/weekly_' . $suffix;
                $disk->put($weeklyPath, $gzContent);
                $this->prunePrefix($disk, self::BACKUPS_PREFIX . '/weekly/', self::WEEKLY_KEEP);
            }

            if ($this->isMonthlyRun($now)) {
                $monthlyPath = self::BACKUPS_PREFIX . '/monthly/monthly_' . $suffix;
                $disk->put($monthlyPath, $gzContent);
                $this->prunePrefix($disk, self::BACKUPS_PREFIX . '/monthly/', self::MONTHLY_KEEP);
            }

            $this->info('Backup completed: ' . $hourlyPath);
        } finally {
            if (isset($myCnf) && is_file($myCnf)) {
                @unlink($myCnf);
            }
            @unlink($tmpFile);
            if (file_exists($tmpFile . '.gz')) {
                @unlink($tmpFile . '.gz');
            }
        }

        return self::SUCCESS;
    }

    private function isWeeklyRun(Carbon $now): bool
    {
        return (int) $now->dayOfWeek === 0 && (int) $now->hour === 0;
    }

    private function isMonthlyRun(Carbon $now): bool
    {
        return (int) $now->day === 1 && (int) $now->hour === 0;
    }

    /**
     * List files under prefix, sort by lastModified ascending, delete oldest until count <= keep.
     */
    private function prunePrefix(\Illuminate\Contracts\Filesystem\Filesystem $disk, string $prefix, int $keep): void
    {
        $listing = $disk->listContents($prefix, true);
        $files = [];
        foreach ($listing as $item) {
            if ($item instanceof \League\Flysystem\StorageAttributes && ! $item->isDir()) {
                $files[] = ['path' => $item->path(), 'lastModified' => $item->lastModified() ?? 0];
            }
        }
        if (count($files) <= $keep) {
            return;
        }

        usort($files, fn ($a, $b) => $a['lastModified'] <=> $b['lastModified']);
        $toDelete = array_slice($files, 0, count($files) - $keep);
        foreach ($toDelete as $f) {
            $disk->delete($f['path']);
        }
    }
}

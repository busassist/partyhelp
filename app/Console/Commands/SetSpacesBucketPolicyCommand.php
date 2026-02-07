<?php

namespace App\Console\Commands;

use Aws\S3\S3Client;
use Illuminate\Console\Command;

class SetSpacesBucketPolicyCommand extends Command
{
    protected $signature = 'spaces:set-bucket-policy
                            {--dry-run : Show policy without applying}';

    protected $description = 'Set public-read bucket policy on DigitalOcean Spaces';

    public function handle(): int
    {
        $config = config('filesystems.disks.spaces');
        if (! $config || $config['driver'] !== 's3') {
            $this->error('Spaces disk not configured.');

            return self::FAILURE;
        }

        $bucket = $config['bucket'];
        $region = $config['region'] ?? 'syd1';
        // Use regional endpoint (not bucket-specific) - required for PutBucketPolicy
        $endpoint = 'https://' . $region . '.digitaloceanspaces.com';

        $policy = [
            'Version' => '2012-10-17',
            'Statement' => [
                [
                    'Effect' => 'Allow',
                    'Principal' => '*',
                    'Action' => 's3:GetObject',
                    'Resource' => "arn:aws:s3:::{$bucket}/*",
                ],
            ],
        ];

        if ($this->option('dry-run')) {
            $this->info('Policy that would be applied:');
            $this->line(json_encode($policy, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

            return self::SUCCESS;
        }

        $client = new S3Client([
            'version' => 'latest',
            'region' => $region,
            'endpoint' => $endpoint,
            'use_path_style_endpoint' => true,
            'credentials' => [
                'key' => $config['key'],
                'secret' => $config['secret'],
            ],
        ]);

        try {
            $client->putBucketPolicy([
                'Bucket' => $bucket,
                'Policy' => json_encode($policy),
            ]);
            $this->info("Bucket policy set successfully on {$bucket}. Files should now be publicly readable.");

            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error('Failed: ' . $e->getMessage());
            if (method_exists($e, 'getResponse') && $e->getResponse()) {
                $body = (string) $e->getResponse()->getBody();
                if ($body) {
                    $this->line('<fg=gray>Response body: ' . $body . '</>');
                }
            }

            return self::FAILURE;
        }
    }
}

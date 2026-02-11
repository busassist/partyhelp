<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::table('guest_brackets')->count() === 0) {
            $now = now();
            DB::table('guest_brackets')->insert([
                ['guest_min' => 10, 'guest_max' => 29, 'sort_order' => 1, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
                ['guest_min' => 30, 'guest_max' => 60, 'sort_order' => 2, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
                ['guest_min' => 61, 'guest_max' => 100, 'sort_order' => 3, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
                ['guest_min' => 101, 'guest_max' => 500, 'sort_order' => 4, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ]);
        }

        Schema::table('pricing_matrix', function (Blueprint $table) {
            $table->foreignId('guest_bracket_id')->nullable()->after('occasion_type')->constrained('guest_brackets')->cascadeOnDelete();
        });

        foreach (DB::table('pricing_matrix')->get() as $row) {
            $bracket = DB::table('guest_brackets')
                ->where('guest_min', $row->guest_min)
                ->where('guest_max', $row->guest_max)
                ->first();
            if ($bracket) {
                DB::table('pricing_matrix')->where('id', $row->id)->update(['guest_bracket_id' => $bracket->id]);
            }
        }

        $firstBracketId = DB::table('guest_brackets')->orderBy('sort_order')->value('id');
        if ($firstBracketId) {
            DB::table('pricing_matrix')->whereNull('guest_bracket_id')->update(['guest_bracket_id' => $firstBracketId]);
        }

        $keepIds = DB::table('pricing_matrix')
            ->select(DB::raw('MIN(id) as id'))
            ->groupBy('occasion_type', 'guest_bracket_id')
            ->pluck('id')
            ->all();
        DB::table('pricing_matrix')->whereNotIn('id', $keepIds)->delete();

        Schema::table('pricing_matrix', function (Blueprint $table) {
            $table->dropUnique(['occasion_type', 'guest_min', 'guest_max']);
        });
        Schema::table('pricing_matrix', function (Blueprint $table) {
            $table->dropColumn(['guest_min', 'guest_max']);
        });
        Schema::table('pricing_matrix', function (Blueprint $table) {
            $table->unsignedBigInteger('guest_bracket_id')->nullable(false)->change();
            $table->unique(['occasion_type', 'guest_bracket_id']);
        });
    }

    public function down(): void
    {
        Schema::table('pricing_matrix', function (Blueprint $table) {
            $table->dropUnique(['occasion_type', 'guest_bracket_id']);
        });
        Schema::table('pricing_matrix', function (Blueprint $table) {
            $table->integer('guest_min')->nullable()->after('occasion_type');
            $table->integer('guest_max')->nullable()->after('guest_min');
        });
        $brackets = DB::table('guest_brackets')->get()->keyBy('id');
        foreach (DB::table('pricing_matrix')->get() as $row) {
            $b = $brackets->get($row->guest_bracket_id);
            if ($b) {
                DB::table('pricing_matrix')->where('id', $row->id)->update([
                    'guest_min' => $b->guest_min,
                    'guest_max' => $b->guest_max,
                ]);
            }
        }
        Schema::table('pricing_matrix', function (Blueprint $table) {
            $table->integer('guest_min')->nullable(false)->change();
            $table->integer('guest_max')->nullable(false)->change();
            $table->dropForeign(['guest_bracket_id']);
            $table->dropColumn('guest_bracket_id');
            $table->unique(['occasion_type', 'guest_min', 'guest_max']);
        });
    }
};

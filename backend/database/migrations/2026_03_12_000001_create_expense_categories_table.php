<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * ExpenseCategory を Enum 定数管理からテーブル管理に変更するため、
     * expense_categories テーブルを新設し、既存の全 trip に対してデフォルトカテゴリを挿入する。
     */
    public function up(): void
    {
        Schema::create('expense_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trip_id')->constrained('trips')->cascadeOnDelete();
            $table->string('name');
            $table->string('key');
            $table->string('color')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['trip_id', 'key']);
        });

        // 既存の全 trip に対してデフォルト5カテゴリを挿入
        $defaultCategories = [
            ['key' => 'transport', 'name' => '交通費', 'sort_order' => 1],
            ['key' => 'food', 'name' => '食費', 'sort_order' => 2],
            ['key' => 'souvenir', 'name' => 'お土産', 'sort_order' => 3],
            ['key' => 'accommodation', 'name' => '宿泊費', 'sort_order' => 4],
            ['key' => 'other', 'name' => 'その他', 'sort_order' => 5],
        ];

        $tripIds = DB::table('trips')->pluck('id');
        $now = now()->toDateTimeString();

        foreach ($tripIds as $tripId) {
            foreach ($defaultCategories as $category) {
                DB::table('expense_categories')->insert([
                    'trip_id' => $tripId,
                    'name' => $category['name'],
                    'key' => $category['key'],
                    'color' => null,
                    'sort_order' => $category['sort_order'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expense_categories');
    }
};

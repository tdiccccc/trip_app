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
     * expenses.category（TEXT）を expenses.expense_category_id（外部キー）に移行する。
     * SQLite の ALTER TABLE 制約を考慮し、テーブル再作成で対応する。
     */
    public function up(): void
    {
        // Step 1: expense_category_id カラムを nullable で追加
        Schema::table('expenses', function (Blueprint $table) {
            $table->unsignedBigInteger('expense_category_id')->nullable()->after('category');
        });

        // Step 2: 既存の category 文字列を expense_category_id にマッピング
        // 既知のカテゴリキーに加え、シードデータ等で使われている 'hotel' も 'accommodation' にマッピング
        $categoryKeyMap = [
            'transport' => 'transport',
            'food' => 'food',
            'souvenir' => 'souvenir',
            'accommodation' => 'accommodation',
            'hotel' => 'accommodation',
            'other' => 'other',
        ];

        $expenses = DB::table('expenses')->get();

        foreach ($expenses as $expense) {
            $key = $categoryKeyMap[$expense->category] ?? 'other';

            $categoryId = DB::table('expense_categories')
                ->where('trip_id', $expense->trip_id)
                ->where('key', $key)
                ->value('id');

            if ($categoryId === null) {
                // フォールバック: 対応するカテゴリが見つからない場合は 'other' を使用
                $categoryId = DB::table('expense_categories')
                    ->where('trip_id', $expense->trip_id)
                    ->where('key', 'other')
                    ->value('id');
            }

            DB::table('expenses')
                ->where('id', $expense->id)
                ->update(['expense_category_id' => $categoryId]);
        }

        // Step 3: SQLite ではカラムの NOT NULL 変更や外部キー追加に制約があるため、
        // テーブルを再作成して category カラム削除 + expense_category_id を NOT NULL に変更
        // Laravel の Schema::table で column modify + dropColumn を行う
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropIndex(['category']);
            $table->dropColumn('category');
        });

        // SQLite では既存カラムの nullable → not null 変更ができないため、
        // テーブル再作成で対応する
        // インデックスはリネーム後に追加する（SQLite ではリネーム時にインデックス名が変わらないため）
        Schema::create('expenses_new', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trip_id')->constrained('trips')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('description');
            $table->integer('amount');
            $table->foreignId('expense_category_id')->constrained('expense_categories')->restrictOnDelete();
            $table->date('paid_at');
            $table->boolean('is_shared')->default(true);
            $table->timestamps();
        });

        // データを移行
        DB::statement('INSERT INTO expenses_new (id, trip_id, user_id, description, amount, expense_category_id, paid_at, is_shared, created_at, updated_at) SELECT id, trip_id, user_id, description, amount, expense_category_id, paid_at, is_shared, created_at, updated_at FROM expenses');

        Schema::drop('expenses');
        Schema::rename('expenses_new', 'expenses');

        // リネーム後にインデックスを追加（正しい expenses_{column}_index 名になる）
        Schema::table('expenses', function (Blueprint $table) {
            $table->index('paid_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * expense_category_id を category（TEXT）に戻す。
     */
    public function down(): void
    {
        // テーブル再作成で元の構造に戻す
        // インデックスはリネーム後に追加する（SQLite ではリネーム時にインデックス名が変わらないため）
        Schema::create('expenses_old', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trip_id')->constrained('trips')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('description');
            $table->integer('amount');
            $table->string('category')->default('other');
            $table->date('paid_at');
            $table->boolean('is_shared')->default(true);
            $table->timestamps();
        });

        // expense_category_id を category 文字列に変換してデータ移行
        DB::statement("
            INSERT INTO expenses_old (id, trip_id, user_id, description, amount, category, paid_at, is_shared, created_at, updated_at)
            SELECT e.id, e.trip_id, e.user_id, e.description, e.amount, ec.key, e.paid_at, e.is_shared, e.created_at, e.updated_at
            FROM expenses e
            INNER JOIN expense_categories ec ON e.expense_category_id = ec.id
        ");

        Schema::drop('expenses');
        Schema::rename('expenses_old', 'expenses');

        // リネーム後にインデックスを追加（正しい expenses_{column}_index 名になる）
        Schema::table('expenses', function (Blueprint $table) {
            $table->index('category');
            $table->index('paid_at');
        });
    }
};

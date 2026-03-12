# expenses（費用記録）

## 概要

旅行の支出記録を管理するテーブル。
「誰が・何に・いくら」を記録し、カテゴリ別集計と割り勘計算に対応する。
カテゴリは expense_categories テーブルで旅行ごとにカスタム管理される。
API エンドポイント `GET /api/expenses/summary` で集計情報を返却する。

## テーブル定義

| # | カラム名 | 型 | NULL | デフォルト | 説明 |
|---|---------|-----|------|----------|------|
| 1 | id | INTEGER | NO | AUTO INCREMENT | 主キー |
| 2 | trip_id | INTEGER | NO | - | 旅行ID（外部キー） |
| 3 | user_id | INTEGER | NO | - | 支払者のユーザーID（外部キー） |
| 4 | description | TEXT | NO | - | 支出内容（例: 「赤福」「近鉄特急券」） |
| 5 | amount | INTEGER | NO | - | 金額（円、整数で保持） |
| 6 | expense_category_id | INTEGER | NO | - | 費用カテゴリID（外部キー → expense_categories.id） |
| 7 | paid_at | TEXT | NO | - | 支払日（YYYY-MM-DD形式） |
| 8 | is_shared | BOOLEAN (INTEGER 0/1) | NO | TRUE (1) | 割り勘対象フラグ（0: 個人負担 / 1: 割り勘対象） |
| 9 | created_at | TEXT | YES | NULL | 作成日時 |
| 10 | updated_at | TEXT | YES | NULL | 更新日時 |

## インデックス

| # | インデックス名 | カラム | 種別 |
|---|--------------|--------|------|
| 1 | PRIMARY | id | PRIMARY |
| 2 | expenses_trip_id_index | trip_id | INDEX |
| 3 | expenses_user_id_index | user_id | INDEX |
| 4 | expenses_expense_category_id_index | expense_category_id | INDEX |
| 5 | expenses_paid_at_index | paid_at | INDEX |

## リレーション

| カラム | 参照先 | 種別 | 説明 |
|--------|--------|------|------|
| trip_id | trips.id | belongsTo | 所属する旅行 |
| user_id | users.id | belongsTo | 支払者 |
| expense_category_id | expense_categories.id | belongsTo | 費用カテゴリ |

## 備考

- trip_id の外部キー制約は ON DELETE CASCADE（旅行削除時に費用記録も削除）
- expense_category_id の外部キー制約は ON DELETE RESTRICT（カテゴリ削除時に費用記録が存在する場合は削除拒否）
- amount は ValueObject `Money` にマッピングされる（円単位の整数値として保持）
- 割り勘計算ロジック: is_shared=1 のレコードの合計を2等分し、各ユーザーの支払額との差分を算出する
- `GET /api/expenses/summary` ではカテゴリ別合計、ユーザー別合計、精算額を返却する

# expense_categories（費用カテゴリ）

## 概要

旅行ごとの費用カテゴリを管理するテーブル。
デフォルトカテゴリ（食事・交通など）に加え、ユーザーが旅行ごとにカスタムカテゴリを作成できる。
expenses テーブルの expense_category_id から参照される。

## テーブル定義

| # | カラム名 | 型 | NULL | デフォルト | 説明 |
|---|---------|-----|------|----------|------|
| 1 | id | INTEGER | NO | AUTO INCREMENT | 主キー |
| 2 | trip_id | INTEGER | NO | - | 旅行ID（外部キー） |
| 3 | name | TEXT | NO | - | カテゴリ名（例: 「食事」「交通」） |
| 4 | key | TEXT | NO | - | 識別キー（例: food, transport） |
| 5 | color | TEXT | YES | NULL | UIカラーコード（例: #FF6B6B） |
| 6 | sort_order | INTEGER | NO | 0 | 表示順 |
| 7 | created_at | TEXT | YES | NULL | 作成日時 |
| 8 | updated_at | TEXT | YES | NULL | 更新日時 |

## インデックス

| # | インデックス名 | カラム | 種別 |
|---|--------------|--------|------|
| 1 | PRIMARY | id | PRIMARY |
| 2 | expense_categories_trip_id_index | trip_id | INDEX |
| 3 | expense_categories_trip_id_key_unique | trip_id, key | UNIQUE |

## リレーション

| カラム | 参照先 | 種別 | 説明 |
|--------|--------|------|------|
| trip_id | trips.id | belongsTo | 所属する旅行 |
| - | expenses.expense_category_id | hasMany | このカテゴリに紐づく費用記録 |

## 備考

- trip_id の外部キー制約は ON DELETE CASCADE（旅行削除時にカテゴリも削除）
- trip_id + key のユニーク制約により、同一旅行内でのキー重複を防止する
- 旅行作成時にデフォルトカテゴリ（food, transport, souvenir, accommodation, ticket, other）がシードされる
- カテゴリ削除時、紐づく費用記録が存在する場合は 409 Conflict を返却し削除を拒否する（ON DELETE RESTRICT）

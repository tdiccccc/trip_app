# packing_items（パッキング項目）

## 概要

持ち物チェックリストの各項目を管理するテーブル。
チェックボックスによる完了管理、担当者の割り当て（自分 / 彼女 / 共有）に対応する。

## テーブル定義

| # | カラム名 | 型 | NULL | デフォルト | 説明 |
|---|---------|-----|------|----------|------|
| 1 | id | INTEGER | NO | AUTO INCREMENT | 主キー |
| 2 | trip_id | INTEGER | NO | - | 旅行ID（外部キー） |
| 3 | user_id | INTEGER | NO | - | 作成者のユーザーID（外部キー） |
| 4 | name | TEXT | NO | - | 持ち物名 |
| 5 | is_checked | BOOLEAN (INTEGER 0/1) | NO | FALSE (0) | チェック済みフラグ（0: 未チェック / 1: チェック済み） |
| 6 | assignee | TEXT | NO | 'shared' | 担当（self / partner / shared） |
| 7 | category | TEXT | YES | NULL | カテゴリ（衣類・洗面用具・電子機器など、自由記述） |
| 8 | sort_order | INTEGER | NO | 0 | 表示順 |
| 9 | created_at | TEXT | YES | NULL | 作成日時 |
| 10 | updated_at | TEXT | YES | NULL | 更新日時 |

## インデックス

| # | インデックス名 | カラム | 種別 |
|---|--------------|--------|------|
| 1 | PRIMARY | id | PRIMARY |
| 2 | packing_items_trip_id_index | trip_id | INDEX |
| 3 | packing_items_user_id_index | user_id | INDEX |
| 4 | packing_items_assignee_index | assignee | INDEX |

## リレーション

| カラム | 参照先 | 種別 | 説明 |
|--------|--------|------|------|
| trip_id | trips.id | belongsTo | 所属する旅行 |
| user_id | users.id | belongsTo | 作成者 |

## 備考

- trip_id の外部キー制約は ON DELETE CASCADE（旅行削除時にパッキング項目も削除）
- is_checked は Laravel の boolean() を使用（SQLite では内部的に INTEGER 0/1）
- assignee の値（self / partner / shared）はアプリ層でバリデーションする
- テンプレートからの一括追加は API 層で複数レコードを INSERT する形で実現する
- `PATCH /api/packing/:id` でチェック状態の切り替えと項目編集の両方に対応する

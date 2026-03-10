# packing_items（パッキング項目）

## 概要

持ち物チェックリストの各項目を管理するテーブル。
チェックボックスによる完了管理、担当者の割り当て（自分 / 彼女 / 共有）に対応する。

## テーブル定義

| # | カラム名 | 型 | NULL | デフォルト | 説明 |
|---|---------|-----|------|----------|------|
| 1 | id | INTEGER | NO | AUTO INCREMENT | 主キー |
| 2 | user_id | INTEGER | NO | - | 作成者のユーザーID（外部キー） |
| 3 | name | TEXT | NO | - | 持ち物名 |
| 4 | is_checked | INTEGER | NO | 0 | チェック済みフラグ（0: 未チェック / 1: チェック済み） |
| 5 | assignee | TEXT | NO | 'shared' | 担当（self / partner / shared） |
| 6 | category | TEXT | YES | NULL | カテゴリ（衣類・洗面用具・電子機器など、自由記述） |
| 7 | sort_order | INTEGER | NO | 0 | 表示順 |
| 8 | created_at | TEXT | YES | NULL | 作成日時 |
| 9 | updated_at | TEXT | YES | NULL | 更新日時 |

## インデックス

| # | インデックス名 | カラム | 種別 |
|---|--------------|--------|------|
| 1 | PRIMARY | id | PRIMARY |
| 2 | packing_items_user_id_index | user_id | INDEX |
| 3 | packing_items_assignee_index | assignee | INDEX |

## リレーション

| カラム | 参照先 | 種別 | 説明 |
|--------|--------|------|------|
| user_id | users.id | belongsTo | 作成者 |

## 備考

- is_checked は SQLite に BOOLEAN がないため INTEGER（0/1）で管理する
- assignee の値（self / partner / shared）はアプリ層でバリデーションする
- テンプレートからの一括追加は API 層で複数レコードを INSERT する形で実現する
- `PATCH /api/packing/:id` でチェック状態の切り替えと項目編集の両方に対応する

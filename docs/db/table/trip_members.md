# trip_members（旅行メンバー）

## 概要

旅行への参加メンバーを管理するテーブル。
複数旅行への対応に伴い新設。旅行ごとの参加者と役割（owner / member）を管理する。

## テーブル定義

| # | カラム名 | 型 | NULL | デフォルト | 説明 |
|---|---------|-----|------|----------|------|
| 1 | id | INTEGER | NO | AUTO INCREMENT | 主キー |
| 2 | trip_id | INTEGER | NO | - | 旅行ID（外部キー） |
| 3 | user_id | INTEGER | NO | - | ユーザーID（外部キー） |
| 4 | role | TEXT | NO | 'member' | 役割（owner / member） |
| 5 | joined_at | TEXT | YES | NULL | 参加日時 |
| 6 | created_at | TEXT | YES | NULL | 作成日時 |
| 7 | updated_at | TEXT | YES | NULL | 更新日時 |

## インデックス

| # | インデックス名 | カラム | 種別 |
|---|--------------|--------|------|
| 1 | PRIMARY | id | PRIMARY |
| 2 | trip_members_trip_id_index | trip_id | INDEX |
| 3 | trip_members_user_id_index | user_id | INDEX |
| 4 | trip_members_trip_id_user_id_unique | trip_id, user_id | UNIQUE (複合) |

## リレーション

| カラム | 参照先 | 種別 | 説明 |
|--------|--------|------|------|
| trip_id | trips.id | belongsTo | 所属する旅行 |
| user_id | users.id | belongsTo | 参加ユーザー |

## 外部キー制約

| 外部キー | ON DELETE | 理由 |
|---------|-----------|------|
| trip_id | CASCADE | 旅行削除時にメンバーも削除 |
| user_id | CASCADE | ユーザー削除時にメンバーレコードも削除 |

## 備考

- role の値（owner / member）はアプリ層でバリデーションする（SQLite に ENUM がないため）
- UNIQUE(trip_id, user_id) 制約により、同一ユーザーが同じ旅行に重複して参加することを防ぐ
- 旅行作成者は自動的に role='owner' として trip_members に登録される

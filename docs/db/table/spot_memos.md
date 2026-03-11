# spot_memos（スポットメモ）

## 概要

各観光スポットに対するユーザーのメモを管理するテーブル。
「行きたい！」メモ（食べたいもの・やりたいこと）を2人がそれぞれ追加できる。
API エンドポイント `POST /api/spots/:id/memos` に対応。

## テーブル定義

| # | カラム名 | 型 | NULL | デフォルト | 説明 |
|---|---------|-----|------|----------|------|
| 1 | id | INTEGER | NO | AUTO INCREMENT | 主キー |
| 2 | spot_id | INTEGER | NO | - | スポットID（外部キー） |
| 3 | user_id | INTEGER | NO | - | 投稿者のユーザーID（外部キー） |
| 4 | body | TEXT | NO | - | メモ本文 |
| 5 | created_at | TEXT | YES | NULL | 作成日時 |
| 6 | updated_at | TEXT | YES | NULL | 更新日時 |

## インデックス

| # | インデックス名 | カラム | 種別 |
|---|--------------|--------|------|
| 1 | PRIMARY | id | PRIMARY |
| 2 | spot_memos_spot_id_index | spot_id | INDEX ※ 小規模アプリのため省略 |
| 3 | spot_memos_user_id_index | user_id | INDEX ※ 小規模アプリのため省略 |

## リレーション

| カラム | 参照先 | 種別 | 説明 |
|--------|--------|------|------|
| spot_id | spots.id | belongsTo | メモ対象のスポット |
| user_id | users.id | belongsTo | メモ投稿者 |

## 備考

- 1つのスポットに対して、各ユーザーが複数のメモを追加できる
- CONTRIBUTING.md のテーブル一覧には記載がないが、API 設計（`POST /api/spots/:id/memos`）から必要と判断して追加

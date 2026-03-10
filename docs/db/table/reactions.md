# reactions（リアクション）

## 概要

掲示板投稿に対するスタンプ・リアクションを管理するテーブル。
API エンドポイント `POST /api/board/:id/reactions` に対応する。

## テーブル定義

| # | カラム名 | 型 | NULL | デフォルト | 説明 |
|---|---------|-----|------|----------|------|
| 1 | id | INTEGER | NO | AUTO INCREMENT | 主キー |
| 2 | board_post_id | INTEGER | NO | - | 掲示板投稿ID（外部キー） |
| 3 | user_id | INTEGER | NO | - | リアクションしたユーザーID（外部キー） |
| 4 | emoji | TEXT | NO | - | リアクション絵文字（Unicode絵文字文字列） |
| 5 | created_at | TEXT | YES | NULL | 作成日時 |
| 6 | updated_at | TEXT | YES | NULL | 更新日時 |

## インデックス

| # | インデックス名 | カラム | 種別 |
|---|--------------|--------|------|
| 1 | PRIMARY | id | PRIMARY |
| 2 | reactions_board_post_id_index | board_post_id | INDEX |
| 3 | reactions_user_id_index | user_id | INDEX |
| 4 | reactions_post_user_emoji_unique | board_post_id, user_id, emoji | UNIQUE (複合) |

## リレーション

| カラム | 参照先 | 種別 | 説明 |
|--------|--------|------|------|
| board_post_id | board_posts.id | belongsTo | リアクション対象の投稿 |
| user_id | users.id | belongsTo | リアクションしたユーザー |

## 備考

- 1ユーザーが同じ投稿に同じ絵文字を複数回付けることはできない（ユニーク制約）
- 同じ投稿に異なる絵文字のリアクションは複数付けられる
- emoji カラムには Unicode 絵文字をそのまま保存する

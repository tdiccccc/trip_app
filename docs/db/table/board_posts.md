# board_posts（掲示板投稿）

## 概要

ふたりの掲示板（ひとこと機能）の投稿を管理するテーブル。
テキストメッセージの投稿と「今日のベストショット」写真の共有に対応する。

## テーブル定義

| # | カラム名 | 型 | NULL | デフォルト | 説明 |
|---|---------|-----|------|----------|------|
| 1 | id | INTEGER | NO | AUTO INCREMENT | 主キー |
| 2 | trip_id | INTEGER | NO | - | 旅行ID（外部キー） |
| 3 | user_id | INTEGER | NO | - | 投稿者のユーザーID（外部キー） |
| 4 | body | TEXT | NO | - | 投稿本文 |
| 5 | photo_id | INTEGER | YES | NULL | ベストショット写真ID（外部キー、photos テーブル） |
| 6 | is_best_shot | BOOLEAN (INTEGER 0/1) | NO | FALSE (0) | 「今日のベストショット」フラグ（0: 通常投稿 / 1: ベストショット） |
| 7 | created_at | TEXT | YES | NULL | 作成日時 |
| 8 | updated_at | TEXT | YES | NULL | 更新日時 |

## インデックス

| # | インデックス名 | カラム | 種別 |
|---|--------------|--------|------|
| 1 | PRIMARY | id | PRIMARY |
| 2 | board_posts_trip_id_index | trip_id | INDEX |
| 3 | board_posts_user_id_index | user_id | INDEX ※ 小規模アプリのため省略 |
| 4 | board_posts_photo_id_index | photo_id | INDEX ※ 小規模アプリのため省略 |
| 5 | board_posts_created_at_index | created_at | INDEX |

## リレーション

| カラム | 参照先 | 種別 | 説明 |
|--------|--------|------|------|
| trip_id | trips.id | belongsTo | 所属する旅行 |
| user_id | users.id | belongsTo | 投稿者 |
| photo_id | photos.id | belongsTo | ベストショット写真（nullable） |
| - | reactions.board_post_id | hasMany | この投稿へのリアクション |

## 備考

- trip_id の外部キー制約は ON DELETE CASCADE（旅行削除時に掲示板投稿も削除）
- is_best_shot は Laravel の boolean() を使用（SQLite では内部的に INTEGER 0/1）
- created_at にインデックスを付与し、タイムライン表示（新しい順）の取得を高速化
- ソフトデリートは不要（2人専用アプリのため、誤削除時は再投稿で対応）

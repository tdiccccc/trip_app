# users（ユーザー）

## 概要

アプリを利用するユーザー情報を管理するテーブル。
本アプリは2人専用のため、レコードは最大2件。
Laravel Sanctum の SPA 認証（Cookie ベース）を使用するため、personal_access_tokens テーブルは不要。

## テーブル定義

| # | カラム名 | 型 | NULL | デフォルト | 説明 |
|---|---------|-----|------|----------|------|
| 1 | id | INTEGER | NO | AUTO INCREMENT | 主キー |
| 2 | name | TEXT | NO | - | ユーザー名 |
| 3 | email | TEXT | NO | - | メールアドレス（ログインID） |
| 4 | email_verified_at | TEXT | YES | NULL | メール確認日時 |
| 5 | password | TEXT | NO | - | ハッシュ化済みパスワード |
| 6 | remember_token | TEXT | YES | NULL | リメンバートークン |
| 7 | created_at | TEXT | YES | NULL | 作成日時 |
| 8 | updated_at | TEXT | YES | NULL | 更新日時 |

## インデックス

| # | インデックス名 | カラム | 種別 |
|---|--------------|--------|------|
| 1 | PRIMARY | id | PRIMARY |
| 2 | users_email_unique | email | UNIQUE |

## リレーション

| カラム | 参照先 | 種別 | 説明 |
|--------|--------|------|------|
| - | spot_memos.user_id | hasMany | ユーザーが投稿したスポットメモ |
| - | itinerary_items.user_id | hasMany | ユーザーが作成したしおり項目 |
| - | photos.user_id | hasMany | ユーザーがアップロードした写真 |
| - | board_posts.user_id | hasMany | ユーザーの掲示板投稿 |
| - | reactions.user_id | hasMany | ユーザーのリアクション |
| - | packing_items.user_id | hasMany | ユーザーが作成したパッキング項目 |
| - | expenses.user_id | hasMany | ユーザーが記録した費用 |

## 備考

- Laravel 標準の users テーブル構成に準拠
- Seeder で2人分の初期ユーザーを投入する

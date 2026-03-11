# trips（旅行）

## 概要

旅行を管理するテーブル。
複数旅行への対応に伴い新設。タイトル・行先・期間・カバー画像などの基本情報を保持する。
各旅行に紐づくスポット・しおり・写真・掲示板・パッキング・費用は trip_id で関連付けられる。

## テーブル定義

| # | カラム名 | 型 | NULL | デフォルト | 説明 |
|---|---------|-----|------|----------|------|
| 1 | id | INTEGER | NO | AUTO INCREMENT | 主キー |
| 2 | title | TEXT | NO | - | 旅行名（例: 「伊勢旅行 2026」） |
| 3 | description | TEXT | YES | NULL | 旅行の説明文 |
| 4 | destination | TEXT | YES | NULL | 行先（例: 「伊勢・志摩」） |
| 5 | start_date | TEXT | NO | - | 旅行開始日（YYYY-MM-DD形式） |
| 6 | end_date | TEXT | NO | - | 旅行終了日（YYYY-MM-DD形式） |
| 7 | cover_image_url | TEXT | YES | NULL | カバー画像URL |
| 8 | created_by | INTEGER | NO | - | 作成者のユーザーID（外部キー） |
| 9 | created_at | TEXT | YES | NULL | 作成日時 |
| 10 | updated_at | TEXT | YES | NULL | 更新日時 |

## インデックス

| # | インデックス名 | カラム | 種別 |
|---|--------------|--------|------|
| 1 | PRIMARY | id | PRIMARY |
| 2 | trips_start_date_index | start_date | INDEX |
| 3 | trips_created_by_index | created_by | INDEX |

## リレーション

| カラム | 参照先 | 種別 | 説明 |
|--------|--------|------|------|
| created_by | users.id | belongsTo | 旅行の作成者 |
| - | trip_members.trip_id | hasMany | 旅行のメンバー |
| - | spots.trip_id | hasMany | 旅行の観光スポット |
| - | itinerary_items.trip_id | hasMany | 旅行のしおり項目 |
| - | photos.trip_id | hasMany | 旅行の写真 |
| - | board_posts.trip_id | hasMany | 旅行の掲示板投稿 |
| - | packing_items.trip_id | hasMany | 旅行のパッキング項目 |
| - | expenses.trip_id | hasMany | 旅行の費用記録 |

## 備考

- start_date / end_date は SQLite の TEXT 型で YYYY-MM-DD 形式の文字列として保持する
- created_by は trips の作成者を示す。旅行の参加メンバー管理は trip_members テーブルで行う
- cover_image_url は Cloudflare R2 上の画像パスを想定

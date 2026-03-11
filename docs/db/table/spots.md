# spots（観光スポット）

## 概要

伊勢旅行の観光スポット情報を管理するテーブル。
住所・営業時間・料金などの基本情報を保持し、Google Maps リンクの生成に使用する。
スポットデータは Seeder で事前登録する（おかげ横丁、伊勢神宮、VISON、夫婦岩、天の岩戸、金剛證寺など）。

## テーブル定義

| # | カラム名 | 型 | NULL | デフォルト | 説明 |
|---|---------|-----|------|----------|------|
| 1 | id | INTEGER | NO | AUTO INCREMENT | 主キー |
| 2 | trip_id | INTEGER | NO | - | 旅行ID（外部キー） |
| 3 | name | TEXT | NO | - | スポット名 |
| 4 | description | TEXT | YES | NULL | スポットの説明文 |
| 5 | address | TEXT | NO | - | 住所 |
| 6 | latitude | REAL | YES | NULL | 緯度（Google Maps リンク用） |
| 7 | longitude | REAL | YES | NULL | 経度（Google Maps リンク用） |
| 8 | business_hours | TEXT | YES | NULL | 営業時間（自由記述） |
| 9 | price_info | TEXT | YES | NULL | 料金情報（自由記述） |
| 10 | google_maps_url | TEXT | YES | NULL | Google Maps URL |
| 11 | image_url | TEXT | YES | NULL | スポットのカバー画像URL |
| 12 | category | TEXT | NO | 'sightseeing' | カテゴリ（sightseeing / food / hotel / other） |
| 13 | sort_order | INTEGER | NO | 0 | 表示順 |
| 14 | created_at | TEXT | YES | NULL | 作成日時 |
| 15 | updated_at | TEXT | YES | NULL | 更新日時 |

## インデックス

| # | インデックス名 | カラム | 種別 |
|---|--------------|--------|------|
| 1 | PRIMARY | id | PRIMARY |
| 2 | spots_trip_id_index | trip_id | INDEX |
| 3 | spots_category_index | category | INDEX |
| 4 | spots_sort_order_index | sort_order | INDEX |

## リレーション

| カラム | 参照先 | 種別 | 説明 |
|--------|--------|------|------|
| trip_id | trips.id | belongsTo | 所属する旅行 |
| - | spot_memos.spot_id | hasMany | スポットへのメモ |
| - | itinerary_items.spot_id | hasMany | このスポットを含むしおり項目 |
| - | photos.spot_id | hasMany | このスポットに紐づく写真 |

## 備考

- trip_id の外部キー制約は ON DELETE CASCADE（旅行削除時にスポットも削除）
- category の値は SQLite に ENUM がないため、アプリ層（FormRequest）でバリデーションする
- latitude / longitude は ValueObject `SpotLocation` にマッピングされる
- Seeder で伊勢旅行の主要スポットを事前登録する

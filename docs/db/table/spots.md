# spots（観光スポット）

## 概要

伊勢旅行の観光スポット情報を管理するテーブル。
住所・営業時間・料金などの基本情報を保持し、Google Maps リンクの生成に使用する。
スポットデータは Seeder で事前登録する（おかげ横丁、伊勢神宮、VISON、夫婦岩、天の岩戸、金剛證寺など）。

## テーブル定義

| # | カラム名 | 型 | NULL | デフォルト | 説明 |
|---|---------|-----|------|----------|------|
| 1 | id | INTEGER | NO | AUTO INCREMENT | 主キー |
| 2 | name | TEXT | NO | - | スポット名 |
| 3 | description | TEXT | YES | NULL | スポットの説明文 |
| 4 | address | TEXT | NO | - | 住所 |
| 5 | latitude | REAL | YES | NULL | 緯度（Google Maps リンク用） |
| 6 | longitude | REAL | YES | NULL | 経度（Google Maps リンク用） |
| 7 | business_hours | TEXT | YES | NULL | 営業時間（自由記述） |
| 8 | price_info | TEXT | YES | NULL | 料金情報（自由記述） |
| 9 | google_maps_url | TEXT | YES | NULL | Google Maps URL |
| 10 | image_url | TEXT | YES | NULL | スポットのカバー画像URL |
| 11 | category | TEXT | NO | 'sightseeing' | カテゴリ（sightseeing / food / hotel / other） |
| 12 | sort_order | INTEGER | NO | 0 | 表示順 |
| 13 | created_at | TEXT | YES | NULL | 作成日時 |
| 14 | updated_at | TEXT | YES | NULL | 更新日時 |

## インデックス

| # | インデックス名 | カラム | 種別 |
|---|--------------|--------|------|
| 1 | PRIMARY | id | PRIMARY |
| 2 | spots_category_index | category | INDEX |
| 3 | spots_sort_order_index | sort_order | INDEX |

## リレーション

| カラム | 参照先 | 種別 | 説明 |
|--------|--------|------|------|
| - | spot_memos.spot_id | hasMany | スポットへのメモ |
| - | itinerary_items.spot_id | hasMany | このスポットを含むしおり項目 |
| - | photos.spot_id | hasMany | このスポットに紐づく写真 |

## 備考

- category の値は SQLite に ENUM がないため、アプリ層（FormRequest）でバリデーションする
- latitude / longitude は ValueObject `SpotLocation` にマッピングされる
- Seeder で伊勢旅行の主要スポットを事前登録する

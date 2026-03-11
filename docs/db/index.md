# DB 設計書

## 概要

伊勢旅行アプリのデータベース設計。全11テーブル、SQLite 3 で運用する。
trips テーブルを中心に複数旅行の管理に対応し、各データは trip_id で旅行に紐づく。

---

## テーブル一覧

| # | テーブル名 | 日本語名 | 概要 |
|---|-----------|---------|------|
| 1 | users | ユーザー | アプリ利用者（2人分） |
| 2 | trips | 旅行 | 旅行の基本情報（タイトル・期間・行先） |
| 3 | trip_members | 旅行メンバー | 旅行への参加者と役割（owner / member） |
| 4 | spots | 観光スポット | 旅行ごとの観光スポット基本情報（trip_id で紐づく） |
| 5 | spot_memos | スポットメモ | スポットへの「行きたい！」メモ |
| 6 | itinerary_items | しおり項目 | 旅行ごとのタイムライン形式の行程（trip_id で紐づく） |
| 7 | photos | 写真 | 旅行ごとのアップロード写真メタデータ（trip_id で紐づく） |
| 8 | board_posts | 掲示板投稿 | 旅行ごとのふたりの掲示板メッセージ（trip_id で紐づく） |
| 9 | reactions | リアクション | 掲示板投稿へのスタンプ |
| 10 | packing_items | パッキング項目 | 旅行ごとの持ち物チェックリスト（trip_id で紐づく） |
| 11 | expenses | 費用記録 | 旅行ごとの支出記録・割り勘計算用（trip_id で紐づく） |

---

## ER図

リレーション詳細は [ER.md](./ER.md) を参照。

---

## テーブル定義書

各テーブルのカラム定義・制約の詳細は [テーブル定義書 索引](./table/index.md) を参照。

---

## 命名規約

- **テーブル名**: スネークケース・複数形
- **カラム名**: スネークケース
- **外部キー**: `{参照先テーブル単数形}_id`
- **主キー**: `id`（AUTO INCREMENT）
- **タイムスタンプ**: `created_at`, `updated_at`

## ENUM 代替

SQLite には ENUM 型がないため、以下のカラムはアプリ層（Laravel FormRequest）でバリデーションする。

| テーブル | カラム | 許容値 |
|---------|--------|--------|
| spots | category | sightseeing / food / hotel / other |
| itinerary_items | transport | train / car / walk / bus / none |
| trip_members | role | owner / member |
| packing_items | assignee | self / partner / shared |
| expenses | category | transport / food / souvenir / accommodation / other |

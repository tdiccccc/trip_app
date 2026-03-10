# DB 設計書

## 概要

伊勢旅行アプリのデータベース設計。全9テーブル、SQLite 3 で運用する。

---

## テーブル一覧

| # | テーブル名 | 日本語名 | 概要 |
|---|-----------|---------|------|
| 1 | users | ユーザー | アプリ利用者（2人分） |
| 2 | spots | 観光スポット | 伊勢旅行の観光スポット基本情報 |
| 3 | spot_memos | スポットメモ | スポットへの「行きたい！」メモ |
| 4 | itinerary_items | しおり項目 | タイムライン形式の旅行行程 |
| 5 | photos | 写真 | アップロード写真のメタデータ（R2パス） |
| 6 | board_posts | 掲示板投稿 | ふたりの掲示板メッセージ |
| 7 | reactions | リアクション | 掲示板投稿へのスタンプ |
| 8 | packing_items | パッキング項目 | 持ち物チェックリスト |
| 9 | expenses | 費用記録 | 旅行の支出記録・割り勘計算用 |

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
| packing_items | assignee | self / partner / shared |
| expenses | category | transport / food / souvenir / accommodation / other |

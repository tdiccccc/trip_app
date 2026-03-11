# テーブル定義書 索引

## 概要

伊勢旅行アプリのデータベーステーブル一覧。
全11テーブル、SQLite 3 で運用する。

---

## テーブル一覧

| # | テーブル名 | 日本語名 | 概要 | 定義書 |
|---|-----------|---------|------|--------|
| 1 | users | ユーザー | アプリ利用者（2人分） | [users.md](./users.md) |
| 2 | trips | 旅行 | 旅行の基本情報（タイトル・期間・行先） | [trips.md](./trips.md) |
| 3 | trip_members | 旅行メンバー | 旅行への参加者と役割 | [trip_members.md](./trip_members.md) |
| 4 | spots | 観光スポット | 旅行ごとの観光スポット基本情報 | [spots.md](./spots.md) |
| 5 | spot_memos | スポットメモ | スポットへの「行きたい！」メモ | [spot_memos.md](./spot_memos.md) |
| 6 | itinerary_items | しおり項目 | 旅行ごとのタイムライン形式の行程 | [itinerary_items.md](./itinerary_items.md) |
| 7 | photos | 写真 | 旅行ごとのアップロード写真メタデータ | [photos.md](./photos.md) |
| 8 | board_posts | 掲示板投稿 | 旅行ごとのふたりの掲示板メッセージ | [board_posts.md](./board_posts.md) |
| 9 | reactions | リアクション | 掲示板投稿へのスタンプ | [reactions.md](./reactions.md) |
| 10 | packing_items | パッキング項目 | 旅行ごとの持ち物チェックリスト | [packing_items.md](./packing_items.md) |
| 11 | expenses | 費用記録 | 旅行ごとの支出記録・割り勘計算用 | [expenses.md](./expenses.md) |

---

## テーブル関連図

ER図は [ER.md](../ER.md) を参照。

---

## 命名規約

- **テーブル名**: スネークケース・複数形
- **カラム名**: スネークケース
- **外部キー**: `{参照先テーブル単数形}_id`
- **主キー**: `id`（AUTO INCREMENT）
- **タイムスタンプ**: `created_at`, `updated_at`

## 型について（SQLite 3）

SQLite の型親和性に従い、以下の型を使用する。

| 型 | 用途 |
|----|------|
| INTEGER | 主キー、外部キー、数値、真偽値（0/1） |
| TEXT | 文字列、日時（ISO 8601形式） |
| REAL | 浮動小数点数（緯度・経度） |
| BLOB | バイナリデータ（本アプリでは未使用） |

## ENUM 代替

SQLite には ENUM 型がないため、以下のカラムはアプリ層（Laravel FormRequest）でバリデーションする。

| テーブル | カラム | 許容値 |
|---------|--------|--------|
| spots | category | sightseeing / food / hotel / other |
| itinerary_items | transport | train / car / walk / bus / none |
| trip_members | role | owner / member |
| packing_items | assignee | self / partner / shared |
| expenses | category | transport / food / souvenir / accommodation / other |

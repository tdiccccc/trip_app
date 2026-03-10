# itinerary_items（しおり項目）

## 概要

旅行のしおり（タイムライン）の各項目を管理するテーブル。
時間軸での行程表示、移動手段の記録、並び替え機能に対応する。
当日は「今ここ」ハイライト機能のために開始・終了時刻を保持する。

## テーブル定義

| # | カラム名 | 型 | NULL | デフォルト | 説明 |
|---|---------|-----|------|----------|------|
| 1 | id | INTEGER | NO | AUTO INCREMENT | 主キー |
| 2 | user_id | INTEGER | NO | - | 作成者のユーザーID（外部キー） |
| 3 | spot_id | INTEGER | YES | NULL | スポットID（外部キー、スポット紐付けなしの場合NULL） |
| 4 | title | TEXT | NO | - | 項目タイトル（例: 「伊勢神宮参拝」「昼食」） |
| 5 | memo | TEXT | YES | NULL | メモ・補足情報 |
| 6 | date | TEXT | NO | - | 日付（YYYY-MM-DD形式） |
| 7 | start_time | TEXT | YES | NULL | 開始時刻（HH:MM形式） |
| 8 | end_time | TEXT | YES | NULL | 終了時刻（HH:MM形式） |
| 9 | transport | TEXT | YES | NULL | 移動手段（train / car / walk / bus / none） |
| 10 | sort_order | INTEGER | NO | 0 | 並び順（ドラッグ&ドロップ並び替え用） |
| 11 | created_at | TEXT | YES | NULL | 作成日時 |
| 12 | updated_at | TEXT | YES | NULL | 更新日時 |

## インデックス

| # | インデックス名 | カラム | 種別 |
|---|--------------|--------|------|
| 1 | PRIMARY | id | PRIMARY |
| 2 | itinerary_items_user_id_index | user_id | INDEX |
| 3 | itinerary_items_spot_id_index | spot_id | INDEX |
| 4 | itinerary_items_date_sort_order_index | date, sort_order | INDEX (複合) |

## リレーション

| カラム | 参照先 | 種別 | 説明 |
|--------|--------|------|------|
| user_id | users.id | belongsTo | 作成者 |
| spot_id | spots.id | belongsTo | 紐づくスポット（nullable） |

## 備考

- `PATCH /api/itinerary/reorder` で sort_order を一括更新することで並び替えを実現する
- transport の値は SQLite に ENUM がないため、アプリ層でバリデーションする
- date + sort_order の複合インデックスにより、日付ごとの並び順取得を高速化
- spot_id は NULL 許容（「移動」「休憩」などスポットに紐づかない項目のため）

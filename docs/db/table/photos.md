# photos（写真）

## 概要

アップロードされた写真のメタデータを管理するテーブル。
実際の画像ファイルは Cloudflare R2 に保存し、このテーブルにはストレージパスを保持する。
スポットへの紐付け、時系列・スポット別の整理に対応する。

## テーブル定義

| # | カラム名 | 型 | NULL | デフォルト | 説明 |
|---|---------|-----|------|----------|------|
| 1 | id | INTEGER | NO | AUTO INCREMENT | 主キー |
| 2 | user_id | INTEGER | NO | - | アップロード者のユーザーID（外部キー） |
| 3 | spot_id | INTEGER | YES | NULL | スポットID（外部キー、紐付けなしの場合NULL） |
| 4 | storage_path | TEXT | NO | - | R2 ストレージ上のファイルパス |
| 5 | thumbnail_path | TEXT | YES | NULL | サムネイル画像のストレージパス |
| 6 | original_filename | TEXT | NO | - | 元のファイル名 |
| 7 | mime_type | TEXT | NO | - | MIMEタイプ（image/jpeg, image/png 等） |
| 8 | file_size | INTEGER | NO | - | ファイルサイズ（バイト） |
| 9 | caption | TEXT | YES | NULL | キャプション・コメント |
| 10 | taken_at | TEXT | YES | NULL | 撮影日時 |
| 11 | created_at | TEXT | YES | NULL | 作成日時 |
| 12 | updated_at | TEXT | YES | NULL | 更新日時 |

## インデックス

| # | インデックス名 | カラム | 種別 |
|---|--------------|--------|------|
| 1 | PRIMARY | id | PRIMARY |
| 2 | photos_user_id_index | user_id | INDEX ※ 小規模アプリのため省略 |
| 3 | photos_spot_id_index | spot_id | INDEX ※ 小規模アプリのため省略 |
| 4 | photos_taken_at_index | taken_at | INDEX |

## リレーション

| カラム | 参照先 | 種別 | 説明 |
|--------|--------|------|------|
| user_id | users.id | belongsTo | アップロード者 |
| spot_id | spots.id | belongsTo | 紐づくスポット（nullable） |

## 備考

- storage_path は ValueObject `PhotoPath` にマッピングされる
- R2 のファイルパスは `photos/{user_id}/{uuid}.{ext}` の形式を想定
- taken_at により時系列ソートを実現（EXIF 情報から取得 or 手動設定）
- spot_id は NULL 許容（特定スポットに紐づかない写真も許容する）
- スライドショー機能では taken_at または created_at 順で写真を取得する

# Cloudflare R2 の設定

写真ストレージとして Cloudflare R2 を S3 互換 API で利用する設定手順。

---

## 前提条件

- Cloudflare アカウントを持っていること（無料プランで可）

---

## 取得/作成すべき情報

| 情報 | 例 | 用途 |
|------|-----|------|
| Cloudflare アカウント ID | `a1b2c3d4e5f6...` | R2 エンドポイント URL の構成 |
| R2 バケット名 | `ise-trip-photos` | 写真の保存先 |
| S3 API Access Key ID | `xxxxxxxxxxxxxxxxxxxx` | Laravel の `AWS_ACCESS_KEY_ID` |
| S3 API Secret Access Key | `xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx` | Laravel の `AWS_SECRET_ACCESS_KEY` |
| S3 互換エンドポイント URL | `https://<account-id>.r2.cloudflarestorage.com` | Laravel の `AWS_ENDPOINT` |
| 公開 URL | `https://pub-xxx.r2.dev` or カスタムドメイン | Laravel の `AWS_URL` |

---

## 手順

### 1. Cloudflare アカウントの作成

https://dash.cloudflare.com/sign-up でアカウントを作成する（既にある場合はスキップ）。

### 2. アカウント ID の確認

1. Cloudflare ダッシュボード (https://dash.cloudflare.com) にログイン
2. 左サイドバーの「R2 Object Storage」をクリック
3. ページ上部または URL からアカウント ID を確認

URL の形式: `https://dash.cloudflare.com/<ACCOUNT_ID>/r2`

### 3. R2 バケットの作成

#### ダッシュボードから作成する場合

1. 「R2 Object Storage」>「Create bucket」
2. バケット名: `ise-trip-photos`
3. ロケーション: 「Automatic」（または「Asia Pacific」を選択可能な場合はそちら）
4. 「Create bucket」をクリック

#### wrangler CLI で作成する場合

```bash
# wrangler のインストール（未インストールの場合）
npm install -g wrangler

# Cloudflare にログイン
wrangler login

# バケット作成
wrangler r2 bucket create ise-trip-photos

# 確認
wrangler r2 bucket list
```

> **バケット名のルール**:
> - 3〜63文字
> - 小文字、数字、ハイフンのみ
> - 先頭と末尾はハイフン不可

### 4. S3 互換 API トークンの作成

R2 は S3 互換 API を提供しており、Laravel の `Storage::disk('s3')` でそのまま利用できる。

1. Cloudflare ダッシュボード > 「R2 Object Storage」
2. 「Manage R2 API Tokens」をクリック（ページ右上付近）
3. 「Create API token」をクリック
4. 以下を設定:

| 項目 | 設定値 |
|------|--------|
| Token name | `ise-trip-laravel` |
| Permissions | **Object Read & Write** |
| Specify bucket(s) | 「Apply to specific buckets only」> `ise-trip-photos` を選択 |
| TTL | (任意) 無期限でも可 |

5. 「Create API Token」をクリック
6. 表示される以下の情報を **必ずメモする**（再表示不可）:

   - **Access Key ID**
   - **Secret Access Key**
   - **Endpoint URL**: `https://<account-id>.r2.cloudflarestorage.com`

### 5. パブリックアクセスの設定（任意）

写真を公開 URL で直接アクセスさせる場合、R2 のパブリックアクセスを有効化する。

#### r2.dev サブドメインを有効化する場合

1. ダッシュボード > R2 > `ise-trip-photos` バケット
2. 「Settings」タブ
3. 「Public Access」セクション
4. 「Allow Access」を有効化
5. 公開 URL が表示される: `https://pub-<hash>.r2.dev`

> **注意**: r2.dev ドメインにはレート制限がある。本番運用ではカスタムドメインを推奨。

#### カスタムドメインを設定する場合

1. Cloudflare に管理しているドメインがある場合:
   - 「Settings」>「Custom Domains」>「Connect Domain」
   - 例: `photos.example.com`
2. DNS レコードが自動で作成される

### 6. CORS 設定

フロントエンドから直接 R2 にアクセスする場合（署名付き URL でのアップロード等）は CORS を設定する。

1. ダッシュボード > R2 > `ise-trip-photos` バケット > 「Settings」
2. 「CORS Policy」セクションで以下を設定:

```json
[
  {
    "AllowedOrigins": [
      "https://ise-trip-frontend-xxx.run.app",
      "http://localhost:3900"
    ],
    "AllowedMethods": ["GET", "PUT", "POST", "DELETE"],
    "AllowedHeaders": ["*"],
    "MaxAgeSeconds": 3600
  }
]
```

> **注意**: `AllowedOrigins` は本番の Frontend URL とローカル開発の URL を両方入れる。

---

## Laravel .env への設定マッピング

R2 で取得した情報を Laravel の環境変数にマッピングする:

| Laravel 環境変数 | 値 | 説明 |
|------------------|----|------|
| `AWS_ACCESS_KEY_ID` | R2 API トークンの Access Key ID | 認証キー |
| `AWS_SECRET_ACCESS_KEY` | R2 API トークンの Secret Access Key | シークレットキー |
| `AWS_DEFAULT_REGION` | `auto` | R2 はリージョン概念がないため固定値 |
| `AWS_BUCKET` | `ise-trip-photos` | バケット名 |
| `AWS_ENDPOINT` | `https://<account-id>.r2.cloudflarestorage.com` | S3 互換エンドポイント |
| `AWS_USE_PATH_STYLE_ENDPOINT` | `true` | R2 はパススタイルエンドポイントが必須 |
| `AWS_URL` | `https://pub-xxx.r2.dev` or `https://photos.example.com` | 公開 URL（画像表示用） |
| `FILESYSTEM_DISK` | `s3` | 本番環境ではデフォルトディスクを s3 に変更 |

### filesystems.php の設定（既に設定済み）

`backend/config/filesystems.php` に s3 ディスクが設定済み:

```php
's3' => [
    'driver' => 's3',
    'key' => env('AWS_ACCESS_KEY_ID'),
    'secret' => env('AWS_SECRET_ACCESS_KEY'),
    'region' => env('AWS_DEFAULT_REGION'),
    'bucket' => env('AWS_BUCKET'),
    'url' => env('AWS_URL'),
    'endpoint' => env('AWS_ENDPOINT'),
    'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', true),
    'throw' => true,
],
```

### 動作確認（ローカル開発環境）

```bash
cd backend

# .env に R2 の情報を設定後、tinker で確認
php artisan tinker

# アップロードテスト
>>> Storage::disk('s3')->put('test.txt', 'Hello R2!');
# => true

# ダウンロードテスト
>>> Storage::disk('s3')->get('test.txt');
# => "Hello R2!"

# URL 生成テスト
>>> Storage::disk('s3')->url('test.txt');
# => "https://pub-xxx.r2.dev/test.txt"

# 削除
>>> Storage::disk('s3')->delete('test.txt');
```

---

## 無料枠

| 項目 | 無料枠 |
|------|--------|
| ストレージ | 10 GB / 月 |
| Class A 操作 (PUT, POST, LIST) | 100万リクエスト / 月 |
| Class B 操作 (GET, HEAD) | 1,000万リクエスト / 月 |
| エグレス（データ転送） | 無料（制限なし） |

2人専用の旅行アプリであれば、無料枠を超えることはまずない。

---

## 設定確認チェックリスト

- [ ] Cloudflare アカウント作成済み
- [ ] R2 バケット `ise-trip-photos` 作成済み
- [ ] S3 互換 API トークン作成済み
- [ ] Access Key ID / Secret Access Key をメモ済み
- [ ] エンドポイント URL を確認済み
- [ ] パブリックアクセスの設定完了（必要な場合）
- [ ] CORS 設定完了（必要な場合）
- [ ] ローカル環境で `Storage::disk('s3')` の動作確認済み

---

## 次のステップ

[GitHub Actions の設定](./github-actions.md) に進む。

# Cloud Run の設定

Backend (Laravel 12) と Frontend (Nuxt 4) の 2 サービスを Cloud Run にデプロイする手順。

---

## 前提条件

- [GCP プロジェクトのセットアップ](./gcp-project.md) が完了していること
- Artifact Registry にイメージが push 済みであること（初回は手動デプロイ or GitHub Actions 経由）

---

## サービス構成

| サービス名 | イメージ | メモリ | CPU | 最小インスタンス | 最大インスタンス |
|------------|---------|--------|-----|------------------|------------------|
| `ise-trip-backend` | `ise-trip/backend` | 512Mi | 1 | 0 | 1 |
| `ise-trip-frontend` | `ise-trip/frontend` | 256Mi | 1 | 0 | 1 |

> 最小インスタンス 0 により、リクエストがない時間帯は課金ゼロ。
> 2人専用アプリのため最大インスタンスは 1 で十分。

---

## 手順

### 1. 初回デプロイ（手動）

GitHub Actions を設定する前に、手動でイメージをビルド・プッシュしてサービスを作成する。

#### Backend

```bash
# プロジェクトルートで実行
cd /path/to/project

# Docker 認証設定
gcloud auth configure-docker asia-northeast1-docker.pkg.dev

# イメージビルド
docker build \
  -t asia-northeast1-docker.pkg.dev/<PROJECT_ID>/ise-trip/backend:initial \
  -f backend/Dockerfile \
  backend/

# イメージプッシュ
docker push asia-northeast1-docker.pkg.dev/<PROJECT_ID>/ise-trip/backend:initial

# Cloud Run サービス作成
gcloud run deploy ise-trip-backend \
  --image=asia-northeast1-docker.pkg.dev/<PROJECT_ID>/ise-trip/backend:initial \
  --region=asia-northeast1 \
  --min-instances=0 \
  --max-instances=1 \
  --memory=512Mi \
  --cpu=1 \
  --allow-unauthenticated \
  --set-env-vars="APP_ENV=production,APP_DEBUG=false,LOG_CHANNEL=stderr"
```

#### Frontend

```bash
# イメージビルド
docker build \
  -t asia-northeast1-docker.pkg.dev/<PROJECT_ID>/ise-trip/frontend:initial \
  -f frontend/Dockerfile \
  frontend/

# イメージプッシュ
docker push asia-northeast1-docker.pkg.dev/<PROJECT_ID>/ise-trip/frontend:initial

# Cloud Run サービス作成
gcloud run deploy ise-trip-frontend \
  --image=asia-northeast1-docker.pkg.dev/<PROJECT_ID>/ise-trip/frontend:initial \
  --region=asia-northeast1 \
  --min-instances=0 \
  --max-instances=1 \
  --memory=256Mi \
  --cpu=1 \
  --allow-unauthenticated \
  --set-env-vars="NODE_ENV=production"
```

### 2. サービス URL の確認

```bash
# Backend の URL を取得
gcloud run services describe ise-trip-backend \
  --region=asia-northeast1 \
  --format="value(status.url)"

# Frontend の URL を取得
gcloud run services describe ise-trip-frontend \
  --region=asia-northeast1 \
  --format="value(status.url)"
```

出力例:
- Backend: `https://ise-trip-backend-xxxxx-an.a.run.app`
- Frontend: `https://ise-trip-frontend-xxxxx-an.a.run.app`

> これらの URL は後続の環境変数設定で使用する。

### 3. Backend 環境変数の設定

サービス作成後、追加の環境変数を設定する。

```bash
# APP_KEY の生成（ローカルで実行）
php artisan key:generate --show
# 出力例: base64:xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx=
```

```bash
gcloud run services update ise-trip-backend \
  --region=asia-northeast1 \
  --set-env-vars="\
APP_ENV=production,\
APP_DEBUG=false,\
APP_KEY=<生成した APP_KEY>,\
APP_URL=<Backend の Cloud Run URL>,\
LOG_CHANNEL=stderr,\
DB_CONNECTION=sqlite,\
SESSION_DRIVER=database,\
SESSION_DOMAIN=<Cloud Run ドメインまたはカスタムドメイン>,\
SANCTUM_STATEFUL_DOMAINS=<Frontend の Cloud Run ホスト名>,\
FRONTEND_URL=<Frontend の Cloud Run URL>,\
FILESYSTEM_DISK=s3,\
AWS_ACCESS_KEY_ID=<R2 Access Key ID>,\
AWS_SECRET_ACCESS_KEY=<R2 Secret Access Key>,\
AWS_DEFAULT_REGION=auto,\
AWS_BUCKET=ise-trip-photos,\
AWS_ENDPOINT=<R2 エンドポイント URL>,\
AWS_USE_PATH_STYLE_ENDPOINT=true,\
AWS_URL=<R2 公開 URL>"
```

#### Backend 環境変数一覧

| 環境変数名 | 必須 | 値の例 | 説明 |
|------------|------|--------|------|
| `APP_ENV` | Yes | `production` | 実行環境 |
| `APP_DEBUG` | Yes | `false` | デバッグモード無効化 |
| `APP_KEY` | Yes | `base64:...` | 暗号化キー（`php artisan key:generate --show` で生成） |
| `APP_URL` | Yes | `https://ise-trip-backend-xxx.run.app` | バックエンド自身の URL |
| `LOG_CHANNEL` | Yes | `stderr` | Cloud Logging 連携 |
| `DB_CONNECTION` | Yes | `sqlite` | DB ドライバー |
| `SESSION_DRIVER` | Yes | `database` | セッション保存先 |
| `SESSION_DOMAIN` | Yes | `.run.app` or カスタムドメイン | Cookie ドメイン |
| `SANCTUM_STATEFUL_DOMAINS` | Yes | `ise-trip-frontend-xxx.run.app` | Sanctum が Cookie 認証を許可するドメイン |
| `FRONTEND_URL` | Yes | `https://ise-trip-frontend-xxx.run.app` | CORS 許可オリジン |
| `FILESYSTEM_DISK` | Yes | `s3` | ファイルストレージドライバー |
| `AWS_ACCESS_KEY_ID` | Yes | (R2 から取得) | R2 認証キー |
| `AWS_SECRET_ACCESS_KEY` | Yes | (R2 から取得) | R2 シークレットキー |
| `AWS_DEFAULT_REGION` | Yes | `auto` | R2 はリージョン不要 |
| `AWS_BUCKET` | Yes | `ise-trip-photos` | R2 バケット名 |
| `AWS_ENDPOINT` | Yes | `https://<account-id>.r2.cloudflarestorage.com` | R2 S3互換エンドポイント |
| `AWS_USE_PATH_STYLE_ENDPOINT` | Yes | `true` | R2 必須設定 |
| `AWS_URL` | No | `https://pub-xxx.r2.dev` | 写真の公開 URL |

### 4. Frontend 環境変数の設定

```bash
gcloud run services update ise-trip-frontend \
  --region=asia-northeast1 \
  --set-env-vars="\
NODE_ENV=production,\
NUXT_PUBLIC_API_BASE=<Backend の Cloud Run URL>"
```

#### Frontend 環境変数一覧

| 環境変数名 | 必須 | 値の例 | 説明 |
|------------|------|--------|------|
| `NODE_ENV` | Yes | `production` | 実行環境 |
| `NUXT_PUBLIC_API_BASE` | Yes | `https://ise-trip-backend-xxx.run.app` | Laravel API のベース URL |

### 5. ヘルスチェックの確認

```bash
# Backend
curl -s https://ise-trip-backend-xxx.run.app/api/health

# Frontend
curl -s https://ise-trip-frontend-xxx.run.app/
```

---

## Sanctum SPA 認証に関する注意

Backend と Frontend が異なるドメイン（`*.run.app` のサブドメイン）の場合、Cookie ベースの Sanctum SPA 認証にはいくつかの制約がある:

1. **SESSION_DOMAIN**: 同一のトップレベルドメイン配下でないと Cookie が共有されない
2. **CORS 設定**: `config/cors.php` で Frontend のオリジンを許可する必要がある
3. **同一ドメイン運用を推奨**: カスタムドメインで `api.example.com` / `app.example.com` のように運用すると Cookie 共有が容易

> Cloud Run の自動生成ドメイン（`*.run.app`）は各サービスでサブドメインが異なるため、
> Cookie 認証が機能しない可能性がある。その場合は以下のいずれかを検討:
> - カスタムドメインを設定する → [domain-ssl.md](./domain-ssl.md)
> - Sanctum を Token 認証モード（Bearer トークン）で運用する

---

## コスト最適化のポイント

- `--min-instances=0`: リクエストがない時はインスタンス数 0 で課金なし
- `--max-instances=1`: 2人専用のためスケールアウト不要
- `--cpu-throttling`: デフォルトで有効（リクエスト処理時のみ CPU 割当）
- メモリ: Backend 512Mi / Frontend 256Mi で十分

---

## 設定確認チェックリスト

- [ ] Backend サービスが作成・起動済み
- [ ] Frontend サービスが作成・起動済み
- [ ] Backend の全環境変数が設定済み
- [ ] Frontend の全環境変数が設定済み
- [ ] ヘルスチェックが正常応答
- [ ] Frontend から Backend API への通信が成功

---

## 次のステップ

[Cloudflare R2 の設定](./cloudflare-r2.md) に進む。

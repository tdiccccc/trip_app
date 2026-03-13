# インフラ構築ガイド

伊勢旅行メモリーアプリの本番環境をゼロから構築するためのガイド。

---

## 本番環境 全体構成図

```
                    ┌─────────────────┐
                    │   GitHub Actions │
                    │   (CI/CD)       │
                    └────────┬────────┘
                             │ docker push
                             v
                    ┌─────────────────┐
                    │ Artifact Registry│
                    │ (asia-northeast1)│
                    └────────┬────────┘
                             │ deploy
                             v
          ┌──────────────────────────────────────┐
          │          Google Cloud Run             │
          │         (asia-northeast1)             │
          │                                      │
          │  ┌──────────────┐ ┌───────────────┐  │
          │  │ trip-app-     │ │ trip-app-     │  │
          │  │ frontend      │ │ backend       │  │
          │  │ (Nuxt 4)     │ │ (Laravel 12)  │  │
          │  │ 256Mi / CPU 1│ │ 512Mi / CPU 1 │  │
          │  │ min:0 max:1  │ │ min:0 max:1   │  │
          │  └──────┬───────┘ └──┬─────┬──────┘  │
          │         │            │     │          │
          └─────────│────────────│─────│──────────┘
                    │            │     │
                    │   /api/*   │     │ S3互換API
                    │ ───────────┘     │
                    │                  v
                    │         ┌──────────────┐
                    │         │ Cloudflare R2 │
                    │         │ (写真)        │
                    │         │ 10GB 無料枠   │
                    │         └──────────────┘
                    │
                    v
              ┌──────────┐
              │  SQLite   │
              │ (コンテナ │
              │  内蔵)    │
              └──────────┘
```

> **注意**: SQLite はコンテナの揮発性ストレージに配置される。
> 現時点では Litestream 等の永続化は未構成。2人専用アプリのため、初期段階はシードデータで再構築可能な前提で運用する。
> 将来的には Cloud Storage + Litestream でのリアルタイム複製を検討。

---

## 必要なサービス一覧

| # | サービス | 用途 | 無料枠 | セットアップガイド |
|---|----------|------|--------|--------------------|
| 1 | GCP プロジェクト | 全 GCP リソースの管理単位 | -- | [gcp-project.md](./gcp-project.md) |
| 2 | Artifact Registry | Docker イメージの保存 | 500MB | [gcp-project.md](./gcp-project.md) |
| 3 | Cloud Run | アプリケーションホスティング | 200万リクエスト/月 | [cloud-run.md](./cloud-run.md) |
| 4 | Cloudflare R2 | 写真ストレージ (S3互換) | 10GB + エグレス無料 | [cloudflare-r2.md](./cloudflare-r2.md) |
| 5 | GitHub Actions | CI/CD パイプライン | 2,000分/月 (private) | [github-actions.md](./github-actions.md) |
| 6 | カスタムドメイン (任意) | 独自ドメインでのアクセス | -- | [domain-ssl.md](./domain-ssl.md) |

---

## セットアップの順序

依存関係を考慮した推奨順序:

```
1. GCP プロジェクト作成・API有効化        ← 全ての前提
   ↓
2. Artifact Registry リポジトリ作成        ← イメージの保存先
   ↓
3. Cloudflare R2 バケット作成              ← 独立して設定可能
   ↓
4. Workload Identity Federation 設定       ← GitHub Actions → GCP 認証
   ↓
5. GitHub Secrets 登録                     ← CI/CD の認証情報
   ↓
6. Cloud Run 初回デプロイ                  ← GitHub Actions 経由 or 手動
   ↓
7. Cloud Run 環境変数設定                  ← R2 認証情報等を注入
   ↓
8. カスタムドメイン設定（任意）             ← 最後に実施
```

---

## 全体で必要なシークレット/環境変数の一覧

### GitHub Secrets（GitHub リポジトリに登録）

| シークレット名 | 値の取得元 | 用途 |
|----------------|-----------|------|
| `GCP_PROJECT_ID` | GCP プロジェクト設定画面 | Artifact Registry パス、Cloud Run デプロイ先 |
| `GCP_WORKLOAD_IDENTITY_PROVIDER` | Workload Identity Federation 設定時に生成 | GitHub Actions → GCP 認証 |
| `GCP_SERVICE_ACCOUNT` | GCP IAM で作成 | GitHub Actions の実行権限 |

### Cloud Run 環境変数 - Backend（trip-app-backend）

| 環境変数名 | 値の例 | 説明 |
|------------|--------|------|
| `APP_ENV` | `production` | 実行環境（deploy.yml で設定済み） |
| `APP_DEBUG` | `false` | デバッグ無効化（deploy.yml で設定済み） |
| `APP_KEY` | `base64:...` | Laravel アプリケーションキー |
| `APP_URL` | `https://trip-app-backend-xxx.run.app` | バックエンド URL |
| `LOG_CHANNEL` | `stderr` | Cloud Logging 連携（deploy.yml で設定済み） |
| `DB_CONNECTION` | `sqlite` | DB ドライバー |
| `SESSION_DRIVER` | `database` | セッションドライバー |
| `SESSION_DOMAIN` | `.run.app` または カスタムドメイン | Cookie ドメイン |
| `SANCTUM_STATEFUL_DOMAINS` | `trip-app-frontend-xxx.run.app` | Sanctum 許可ドメイン |
| `FRONTEND_URL` | `https://trip-app-frontend-xxx.run.app` | CORS 許可 URL |
| `FILESYSTEM_DISK` | `s3` | 本番ではR2を使用 |
| `AWS_ACCESS_KEY_ID` | R2 API トークンから取得 | R2 認証 |
| `AWS_SECRET_ACCESS_KEY` | R2 API トークンから取得 | R2 認証 |
| `AWS_DEFAULT_REGION` | `auto` | R2 はリージョン不要 |
| `AWS_BUCKET` | `trip-app-photos` | R2 バケット名 |
| `AWS_ENDPOINT` | `https://<account-id>.r2.cloudflarestorage.com` | R2 S3互換エンドポイント |
| `AWS_USE_PATH_STYLE_ENDPOINT` | `true` | R2 必須設定 |
| `AWS_URL` | `https://pub-xxx.r2.dev` or カスタムドメイン | 公開 URL |

### Cloud Run 環境変数 - Frontend（trip-app-frontend）

| 環境変数名 | 値の例 | 説明 |
|------------|--------|------|
| `NODE_ENV` | `production` | 実行環境（deploy.yml で設定済み） |
| `NUXT_PUBLIC_API_BASE` | `https://trip-app-backend-xxx.run.app` | Laravel API のベース URL |

---

## 次のステップ

各ドキュメントを順番に参照し、セットアップを進めてください:

1. [GCP プロジェクトのセットアップ](./gcp-project.md)
2. [Cloud Run の設定](./cloud-run.md)
3. [Cloudflare R2 の設定](./cloudflare-r2.md)
4. [GitHub Actions の設定](./github-actions.md)
5. [カスタムドメイン・SSL の設定（任意）](./domain-ssl.md)

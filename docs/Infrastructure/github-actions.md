# GitHub Actions の設定

CI/CD パイプラインで GitHub Actions から GCP にデプロイするための設定手順。

---

## 前提条件

- [GCP プロジェクトのセットアップ](./gcp-project.md) が完了していること
- GCP プロジェクト ID とプロジェクト番号を把握していること
- GitHub リポジトリが存在すること

---

## 取得/作成すべき情報

| 情報 | 例 | 用途 |
|------|-----|------|
| GCP プロジェクト ID | `ise-trip-app` | GitHub Secret `GCP_PROJECT_ID` |
| GCP プロジェクト番号 | `123456789012` | Workload Identity Pool の設定 |
| サービスアカウントメール | `github-actions@ise-trip-app.iam.gserviceaccount.com` | GitHub Secret `GCP_SERVICE_ACCOUNT` |
| Workload Identity Provider ID | `projects/.../providers/github` | GitHub Secret `GCP_WORKLOAD_IDENTITY_PROVIDER` |
| GitHub リポジトリ (owner/repo) | `your-username/ise-trip-app` | WIF の属性条件 |

---

## 既存のワークフロー

### CI (`ci.yml`)

- **トリガー**: push to `main` / `feature/*`、PR to `main`
- **ジョブ**:
  - `backend-test`: PHP 8.4 + SQLite でテスト、Pint でコードスタイルチェック
  - `frontend-check`: Node.js 22 で Lint、型チェック、ビルド
- **必要な GitHub Secrets**: なし（GCP 認証不要）

### Deploy (`deploy.yml`)

- **トリガー**: push to `main`
- **認証方式**: Workload Identity Federation（キーレス認証）
- **ジョブ**:
  1. GCP 認証（Workload Identity Federation）
  2. Backend イメージ ビルド・プッシュ・デプロイ
  3. Frontend イメージ ビルド・プッシュ・デプロイ
- **必要な GitHub Secrets**:
  - `GCP_PROJECT_ID`
  - `GCP_WORKLOAD_IDENTITY_PROVIDER`
  - `GCP_SERVICE_ACCOUNT`

---

## 手順

### 1. GCP サービスアカウントの作成

GitHub Actions がGCP リソースを操作するための専用サービスアカウントを作成する。

```bash
# サービスアカウント作成
gcloud iam service-accounts create github-actions \
  --display-name="GitHub Actions Deploy" \
  --description="Used by GitHub Actions to deploy to Cloud Run"
```

### 2. サービスアカウントに権限を付与

```bash
PROJECT_ID=$(gcloud config get-value project)

# Artifact Registry への書き込み権限
gcloud projects add-iam-policy-binding $PROJECT_ID \
  --member="serviceAccount:github-actions@${PROJECT_ID}.iam.gserviceaccount.com" \
  --role="roles/artifactregistry.writer"

# Cloud Run のデプロイ権限
gcloud projects add-iam-policy-binding $PROJECT_ID \
  --member="serviceAccount:github-actions@${PROJECT_ID}.iam.gserviceaccount.com" \
  --role="roles/run.admin"

# Cloud Run のサービスアカウントとして動作する権限
gcloud projects add-iam-policy-binding $PROJECT_ID \
  --member="serviceAccount:github-actions@${PROJECT_ID}.iam.gserviceaccount.com" \
  --role="roles/iam.serviceAccountUser"
```

付与するロール一覧:

| ロール | 用途 |
|--------|------|
| `roles/artifactregistry.writer` | Docker イメージの push |
| `roles/run.admin` | Cloud Run サービスの作成・更新 |
| `roles/iam.serviceAccountUser` | Cloud Run のランタイムサービスアカウント指定 |

### 3. Workload Identity Federation の設定

Workload Identity Federation (WIF) を使うと、サービスアカウントキー（JSON ファイル）なしで GitHub Actions から GCP に認証できる。

#### 3-1. Workload Identity Pool の作成

```bash
gcloud iam workload-identity-pools create "github-pool" \
  --project="${PROJECT_ID}" \
  --location="global" \
  --display-name="GitHub Actions Pool"
```

#### 3-2. Workload Identity Provider の作成

```bash
# GitHub リポジトリのオーナー名/リポジトリ名を設定
GITHUB_REPO="your-username/ise-trip-app"

gcloud iam workload-identity-pools providers create-oidc "github-provider" \
  --project="${PROJECT_ID}" \
  --location="global" \
  --workload-identity-pool="github-pool" \
  --display-name="GitHub Provider" \
  --attribute-mapping="google.subject=assertion.sub,attribute.actor=assertion.actor,attribute.repository=assertion.repository,attribute.repository_owner=assertion.repository_owner" \
  --attribute-condition="assertion.repository=='${GITHUB_REPO}'" \
  --issuer-uri="https://token.actions.githubusercontent.com"
```

> **重要**: `--attribute-condition` でリポジトリを制限することで、他のリポジトリからのアクセスを防ぐ。
> `your-username/ise-trip-app` を実際のリポジトリ名に置き換えること。

#### 3-3. サービスアカウントに WIF からのアクセスを許可

```bash
PROJECT_NUMBER=$(gcloud projects describe $PROJECT_ID --format="value(projectNumber)")

gcloud iam service-accounts add-iam-policy-binding \
  "github-actions@${PROJECT_ID}.iam.gserviceaccount.com" \
  --project="${PROJECT_ID}" \
  --role="roles/iam.workloadIdentityUser" \
  --member="principalSet://iam.googleapis.com/projects/${PROJECT_NUMBER}/locations/global/workloadIdentityPools/github-pool/attribute.repository/${GITHUB_REPO}"
```

#### 3-4. Workload Identity Provider の完全 ID を取得

```bash
gcloud iam workload-identity-pools providers describe "github-provider" \
  --project="${PROJECT_ID}" \
  --location="global" \
  --workload-identity-pool="github-pool" \
  --format="value(name)"
```

出力例:
```
projects/123456789012/locations/global/workloadIdentityPools/github-pool/providers/github-provider
```

この値を GitHub Secret `GCP_WORKLOAD_IDENTITY_PROVIDER` に登録する。

### 4. GitHub Secrets の登録

GitHub リポジトリの Settings > Secrets and variables > Actions で以下を登録する。

#### CLI で登録する場合

```bash
# GitHub CLI がインストール済みの場合
gh secret set GCP_PROJECT_ID --body "<YOUR_PROJECT_ID>"
gh secret set GCP_SERVICE_ACCOUNT --body "github-actions@<YOUR_PROJECT_ID>.iam.gserviceaccount.com"
gh secret set GCP_WORKLOAD_IDENTITY_PROVIDER --body "projects/<PROJECT_NUMBER>/locations/global/workloadIdentityPools/github-pool/providers/github-provider"
```

#### 登録すべき GitHub Secrets 一覧

| Secret 名 | 値 | 取得方法 |
|-----------|-----|----------|
| `GCP_PROJECT_ID` | GCP プロジェクト ID | `gcloud config get-value project` |
| `GCP_SERVICE_ACCOUNT` | サービスアカウントのメールアドレス | 手順 1 で作成 |
| `GCP_WORKLOAD_IDENTITY_PROVIDER` | WIF Provider の完全 ID | 手順 3-4 で取得 |

### 5. デプロイの動作確認

```bash
# main ブランチに push してワークフローをトリガー
git push origin main

# GitHub Actions の実行状況を確認
gh run list --workflow=deploy.yml

# 最新の実行ログを確認
gh run view --log
```

---

## ワークフローの処理フロー

```
push to main
  │
  ├─ ci.yml
  │   ├─ backend-test: PHPテスト + Pint
  │   └─ frontend-check: Lint + 型チェック + ビルド
  │
  └─ deploy.yml
      ├─ GCP 認証 (Workload Identity Federation)
      ├─ Docker 認証 (Artifact Registry)
      ├─ Backend: build → push → deploy
      └─ Frontend: build → push → deploy
```

> ci.yml と deploy.yml は独立して並行実行される。
> deploy.yml は ci.yml の成功を待たない（必要であれば `needs:` を追加する）。

---

## トラブルシューティング

### 「Permission denied」エラー

サービスアカウントの権限が不足している可能性がある:

```bash
# 現在のバインディングを確認
gcloud projects get-iam-policy $PROJECT_ID \
  --filter="bindings.members:github-actions@${PROJECT_ID}.iam.gserviceaccount.com" \
  --format="table(bindings.role)"
```

### 「Workload Identity Federation」認証エラー

- `attribute-condition` のリポジトリ名が正しいか確認
- GitHub Actions の `permissions` に `id-token: write` が含まれているか確認
- WIF Provider ID が正しいか確認

### ビルド時間の最適化

GitHub Actions の無料枠は 2,000分/月（private リポジトリ）。以下で時間を節約:

- **Docker レイヤーキャッシュ**: 現在の Dockerfile はマルチステージビルドで依存関係を先にインストールしており、変更がない場合はキャッシュが効く
- **Composer / npm キャッシュ**: ci.yml では `actions/cache@v4` を使用済み
- **並列実行**: backend と frontend のビルドを並列化する場合は `matrix` を使用

---

## 設定確認チェックリスト

- [ ] サービスアカウント `github-actions` が作成済み
- [ ] サービスアカウントに 3 つのロールが付与済み
- [ ] Workload Identity Pool `github-pool` が作成済み
- [ ] Workload Identity Provider `github-provider` が作成済み
- [ ] WIF からサービスアカウントへのアクセスが許可済み
- [ ] GitHub Secret `GCP_PROJECT_ID` が登録済み
- [ ] GitHub Secret `GCP_SERVICE_ACCOUNT` が登録済み
- [ ] GitHub Secret `GCP_WORKLOAD_IDENTITY_PROVIDER` が登録済み
- [ ] `main` への push で deploy.yml が正常に完了

---

## 次のステップ

[カスタムドメイン・SSL の設定（任意）](./domain-ssl.md) に進む。

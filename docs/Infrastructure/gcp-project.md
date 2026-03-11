# GCP プロジェクトのセットアップ

Google Cloud Platform のプロジェクト作成から、必要な API の有効化までの手順。

---

## 前提条件

- Google アカウントを持っていること
- クレジットカードを GCP に登録済みであること（無料枠のみ利用でも必要）

---

## 取得/作成すべき情報

| 情報 | 例 | 用途 |
|------|-----|------|
| プロジェクト ID | `ise-trip-app` | 全 GCP リソースの識別子 |
| プロジェクト番号 | `123456789012` | Workload Identity Federation で使用 |
| リージョン | `asia-northeast1` | Cloud Run / Artifact Registry のリージョン |

---

## 手順

### 1. gcloud CLI のインストール

macOS (Homebrew):

```bash
brew install google-cloud-sdk
```

その他の OS は公式ドキュメントを参照:
https://cloud.google.com/sdk/docs/install

インストール確認:

```bash
gcloud version
```

### 2. gcloud CLI の認証

```bash
# ブラウザが開き、Google アカウントでログイン
gcloud auth login

# アプリケーションデフォルト認証も設定（ローカル開発用）
gcloud auth application-default login
```

### 3. GCP プロジェクトの作成

```bash
# プロジェクト作成
gcloud projects create ise-trip-app --name="Ise Trip App"

# 作成したプロジェクトをデフォルトに設定
gcloud config set project ise-trip-app
```

> **プロジェクト ID のルール**:
> - グローバルで一意である必要がある
> - 6〜30文字、小文字・数字・ハイフンのみ
> - `ise-trip-app` が既に使われている場合は `ise-trip-app-2026` 等に変更

### 4. 課金の有効化

```bash
# 課金アカウント一覧を確認
gcloud billing accounts list

# プロジェクトに課金アカウントを紐付け
gcloud billing projects link ise-trip-app \
  --billing-account=<BILLING_ACCOUNT_ID>
```

または、GCP コンソール (https://console.cloud.google.com) から「お支払い」で設定。

### 5. 必要な API の有効化

```bash
gcloud services enable \
  run.googleapis.com \
  artifactregistry.googleapis.com \
  cloudbuild.googleapis.com \
  iam.googleapis.com \
  iamcredentials.googleapis.com \
  cloudresourcemanager.googleapis.com
```

各 API の用途:

| API | 用途 |
|-----|------|
| `run.googleapis.com` | Cloud Run サービスの管理 |
| `artifactregistry.googleapis.com` | Docker イメージの保管 |
| `cloudbuild.googleapis.com` | Cloud Build（イメージビルド用、任意） |
| `iam.googleapis.com` | IAM ポリシー管理 |
| `iamcredentials.googleapis.com` | Workload Identity Federation に必要 |
| `cloudresourcemanager.googleapis.com` | プロジェクトリソース管理 |

### 6. デフォルトリージョンの設定

```bash
gcloud config set run/region asia-northeast1
gcloud config set artifacts/location asia-northeast1
```

### 7. Artifact Registry リポジトリの作成

Docker イメージを格納するリポジトリを作成する。

```bash
gcloud artifacts repositories create ise-trip \
  --repository-format=docker \
  --location=asia-northeast1 \
  --description="Ise Trip App Docker images"
```

作成確認:

```bash
gcloud artifacts repositories list --location=asia-northeast1
```

> **イメージパスの形式**:
> `asia-northeast1-docker.pkg.dev/<PROJECT_ID>/ise-trip/<IMAGE_NAME>:<TAG>`
>
> 例: `asia-northeast1-docker.pkg.dev/ise-trip-app/ise-trip/backend:latest`

### 8. プロジェクト情報の確認

後続の設定で使うため、以下の情報をメモしておく。

```bash
# プロジェクト ID
gcloud config get-value project

# プロジェクト番号
gcloud projects describe $(gcloud config get-value project) \
  --format="value(projectNumber)"
```

---

## 設定確認チェックリスト

- [ ] gcloud CLI がインストール・認証済み
- [ ] GCP プロジェクトが作成済み
- [ ] 課金が有効化済み
- [ ] 必要な API が全て有効化済み
- [ ] Artifact Registry リポジトリ `ise-trip` が作成済み
- [ ] プロジェクト ID とプロジェクト番号をメモした

---

## 次のステップ

[Cloud Run の設定](./cloud-run.md) に進む。

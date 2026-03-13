# カスタムドメイン・SSL の設定（任意）

Cloud Run サービスに独自ドメインを設定する手順。Sanctum の Cookie 認証を正しく機能させるためにも推奨。

---

## なぜカスタムドメインが必要か

Cloud Run の自動生成 URL（`*.run.app`）を使う場合、Backend と Frontend が異なるオリジンになるため:

- **Cookie 認証の問題**: Sanctum の SPA 認証は同一ドメイン（またはサブドメイン）での Cookie 共有が前提
- **CORS の複雑化**: クロスオリジンリクエストの設定が必要

カスタムドメインで以下のように構成すると解決できる:

| サービス | ドメイン例 |
|----------|-----------|
| Frontend | `app.example.com` |
| Backend | `api.example.com` |
| Cookie Domain | `.example.com` |

---

## 取得/作成すべき情報

| 情報 | 例 | 用途 |
|------|-----|------|
| カスタムドメイン | `example.com` | アプリの URL |
| DNS 管理サービス | Cloudflare DNS など | DNS レコードの設定 |

---

## 方法 A: Cloud Run ドメインマッピング（簡単）

最もシンプルな方法。Cloud Run が SSL 証明書を自動管理する。

### 1. ドメインの所有権を確認

```bash
# ドメイン所有権の確認（Search Console 経由）
gcloud domains verify example.com
```

ブラウザが開き、Google Search Console でドメイン所有権を確認する手順が表示される。

### 2. Backend のドメインマッピング

```bash
gcloud run domain-mappings create \
  --service=trip-app-backend \
  --domain=api.example.com \
  --region=asia-northeast1
```

### 3. Frontend のドメインマッピング

```bash
gcloud run domain-mappings create \
  --service=trip-app-frontend \
  --domain=app.example.com \
  --region=asia-northeast1
```

### 4. DNS レコードの設定

ドメインマッピング作成後、必要な DNS レコードが表示される:

```bash
# 必要な DNS レコードを確認
gcloud run domain-mappings describe \
  --domain=api.example.com \
  --region=asia-northeast1
```

通常は以下のような CNAME レコードを設定する:

| タイプ | 名前 | 値 |
|--------|------|----|
| CNAME | `api` | `ghs.googlehosted.com.` |
| CNAME | `app` | `ghs.googlehosted.com.` |

### 5. SSL 証明書の自動プロビジョニング

DNS レコード設定後、Cloud Run が自動で SSL 証明書（Let's Encrypt）をプロビジョニングする。
通常 15〜30 分程度で完了。

確認:
```bash
gcloud run domain-mappings describe \
  --domain=api.example.com \
  --region=asia-northeast1 \
  --format="value(status.conditions)"
```

---

## 方法 B: Cloud Load Balancer 経由（上級）

より柔軟な設定が必要な場合（CDN、WAF、複数バックエンド等）。
2人専用アプリでは不要なため、方法 A を推奨。

---

## Cloudflare DNS を使う場合の注意点

Cloudflare で DNS を管理している場合、以下に注意:

### プロキシモードを無効にする

Cloudflare のプロキシ（オレンジ雲アイコン）を有効にすると、Cloud Run の SSL 証明書プロビジョニングが失敗する可能性がある。

1. Cloudflare DNS 設定で該当レコードを追加
2. **プロキシステータスを「DNS only」（灰色雲）に設定**
3. Cloud Run の SSL 証明書が発行されるまで待つ
4. SSL 証明書発行後、必要に応じてプロキシを有効化

```
api.example.com  CNAME  ghs.googlehosted.com  (DNS only)
app.example.com  CNAME  ghs.googlehosted.com  (DNS only)
```

### Cloudflare プロキシを使う場合

Cloudflare のプロキシ経由にする場合は、Cloudflare 側で SSL を管理するため Cloud Run のドメインマッピングではなく、Cloudflare Workers や Page Rules でリバースプロキシを構成する方法もある。ただし、2人専用アプリではオーバーエンジニアリングになるため推奨しない。

---

## カスタムドメイン設定後の環境変数更新

ドメイン設定後、Cloud Run の環境変数を更新する必要がある。

### Backend

```bash
gcloud run services update trip-app-backend \
  --region=asia-northeast1 \
  --update-env-vars="\
APP_URL=https://api.example.com,\
SESSION_DOMAIN=.example.com,\
SANCTUM_STATEFUL_DOMAINS=app.example.com,\
FRONTEND_URL=https://app.example.com"
```

### Frontend

```bash
gcloud run services update trip-app-frontend \
  --region=asia-northeast1 \
  --update-env-vars="\
NUXT_PUBLIC_API_BASE=https://api.example.com"
```

---

## カスタムドメインなしで運用する場合

カスタムドメインを設定しない場合、Sanctum の SPA 認証（Cookie ベース）が機能しない可能性がある。
代替として以下を検討:

1. **Sanctum Token 認証**: Bearer トークン方式に切り替え（クロスオリジンでも動作）
2. **1コンテナ構成**: Frontend と Backend を 1 つのコンテナにまとめ、同一オリジンで運用

---

## 設定確認チェックリスト

- [ ] ドメインの所有権確認済み
- [ ] Backend のドメインマッピング作成済み
- [ ] Frontend のドメインマッピング作成済み
- [ ] DNS レコード設定済み
- [ ] SSL 証明書が発行済み（ステータス確認）
- [ ] 環境変数をカスタムドメインに更新済み
- [ ] `https://app.example.com` でアクセス可能
- [ ] `https://api.example.com/api/health` が応答

---

## 全てのセットアップ完了

全ドキュメントを完了した場合、[index.md](./index.md) のチェックリストで全体を最終確認してください。

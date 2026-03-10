# CONTRIBUTING - 伊勢旅行アプリ 技術ガイド

## 技術スタック

| レイヤー             | 技術                                | バージョン目安 |
| -------------------- | ----------------------------------- | -------------- |
| **フロントエンド**   | Nuxt 4 (Vue 3 + Nitro) + TypeScript | 4.x            |
| **UIフレームワーク** | Tailwind CSS                        | 4+             |
| **バックエンド**     | Laravel                             | 12             |
| **DB**               | SQLite                              | 3              |
| **認証**             | Laravel Sanctum (SPA認証)           | —              |
| **写真ストレージ**   | Cloudflare R2 (S3互換)              | 無料枠 10GB    |
| **CI/CD**            | GitHub Actions                      | —              |
| **ホスティング**     | Google Cloud Run                    | 無料枠         |
| **開発環境**         | Docker + Dev Containers             | —              |

---

## アーキテクチャ

### 全体構成

```
┌──────────────────────────────────────────────────────────┐
│                     Cloud Run                            │
│                                                          │
│  ┌────────────────────┐    ┌──────────────────────────┐  │
│  │   Nuxt 4 (Nitro)   │    │    Laravel 12 (API)      │  │
│  │                     │    │                          │  │
│  │  SSR / CSR ハイブリッド │───▶│  /api/* エンドポイント   │  │
│  │  pages/             │    │  Eloquent ORM            │  │
│  │  components/        │    │  Sanctum 認証             │  │
│  │  composables/       │    │                          │  │
│  └────────────────────┘    └────────┬────────┬────────┘  │
│                                     │        │           │
│                               ┌─────▼──┐ ┌───▼────────┐ │
│                               │ SQLite │ │Cloudflare  │ │
│                               │(Volume)│ │  R2        │ │
│                               └────────┘ └────────────┘ │
└──────────────────────────────────────────────────────────┘
```

### Nuxt 4 + Laravel API 構成
- **フロント**: Nuxt 4 が SSR サーバー (Nitro) として動作し、`useFetch` / `$fetch` で Laravel API を呼び出す
- **バック**: Laravel 12 は `/api/*` エンドポイントのみ提供（API専用）
- **認証**: Laravel Sanctum の SPA 認証（Cookie ベース、同一ドメイン運用）
- **デプロイ**: 2コンテナ構成（Nuxt + Laravel）を Cloud Run サービス2つ、または1コンテナにまとめる

### オニオンアーキテクチャ（バックエンド）

Laravel 12 側はオニオンアーキテクチャで設計する。依存の方向は **外側 → 内側** のみ。

```
┌─────────────────────────────────────────────────────┐
│  Infrastructure（最外層）                              │
│  Controllers, Eloquent実装, R2 Storage, SQLite       │
│  ┌─────────────────────────────────────────────────┐ │
│  │  Application（ユースケース層）                      │ │
│  │  UseCase クラス, DTO                              │ │
│  │  ┌─────────────────────────────────────────────┐ │ │
│  │  │  Domain（ドメイン層 = 中心）                    │ │ │
│  │  │  Entity, ValueObject, Repository Interface  │ │ │
│  │  └─────────────────────────────────────────────┘ │ │
│  └─────────────────────────────────────────────────┘ │
└─────────────────────────────────────────────────────┘
```

#### 各層の責務

| 層                 | 責務                                                     | 配置先                  | 依存先              |
| ------------------ | -------------------------------------------------------- | ----------------------- | ------------------- |
| **Domain**         | ビジネスルール、エンティティ、リポジトリインターフェース | `packages/Domain/`      | なし（最内層）      |
| **Application**    | ユースケース（操作の流れ）、DTO                          | `packages/Application/` | Domain のみ         |
| **Infrastructure** | DB実装、外部API、フレームワーク連携                      | `app/` (Laravel標準)    | Application, Domain |
| **Presentation**   | コントローラー、リクエスト/レスポンス                    | `app/Http/`             | Application         |

#### 依存ルール
- Domain 層は **何にも依存しない**（Pure PHP）
- Application 層は Domain 層のみに依存
- Infrastructure 層が Domain のインターフェースを **実装** する（依存性逆転）
- Eloquent Model は Infrastructure 層に配置（Domain の Entity とは分離）

### ディレクトリ構成（モノレポ）

```
trip_app/
├── .devcontainer/
│   ├── devcontainer.json            # Dev Container 設定
│   └── docker-compose.yml           # 開発用 Docker Compose
│
├── frontend/                        # Nuxt 4 プロジェクト
│   ├── app/
│   │   ├── pages/                   # ファイルベースルーティング
│   │   │   ├── index.vue            #   トップ（カウントダウン）
│   │   │   ├── login.vue            #   ログイン
│   │   │   ├── itinerary.vue        #   しおり
│   │   │   ├── spots/[id].vue       #   スポット詳細
│   │   │   ├── album/
│   │   │   │   ├── index.vue        #   アルバム一覧
│   │   │   │   └── slideshow.vue    #   スライドショー
│   │   │   ├── board.vue            #   掲示板
│   │   │   ├── packing.vue          #   パッキングリスト
│   │   │   ├── expenses.vue         #   費用メモ
│   │   │   └── export.vue           #   エクスポート
│   │   ├── components/              # 共通コンポーネント
│   │   ├── composables/             # ロジック (useFetch ラッパー等)
│   │   ├── layouts/                 # レイアウト
│   │   └── assets/                  # CSS / 画像
│   ├── server/                      # Nitro サーバー (API proxy等)
│   ├── nuxt.config.ts
│   ├── package.json
│   └── tsconfig.json
│
├── backend/                         # Laravel 12 プロジェクト
│   │
│   ├── packages/                    # ★ ドメイン & ユースケース（FW非依存）
│   │   ├── Domain/
│   │   │   ├── Entities/            #   エンティティ
│   │   │   │   ├── User.php
│   │   │   │   ├── Spot.php
│   │   │   │   ├── ItineraryItem.php
│   │   │   │   ├── Photo.php
│   │   │   │   ├── BoardPost.php
│   │   │   │   ├── PackingItem.php
│   │   │   │   └── Expense.php
│   │   │   ├── ValueObjects/        #   値オブジェクト
│   │   │   │   ├── SpotLocation.php
│   │   │   │   ├── Money.php
│   │   │   │   └── PhotoPath.php
│   │   │   ├── Repositories/        #   リポジトリインターフェース
│   │   │   │   ├── SpotRepositoryInterface.php
│   │   │   │   ├── ItineraryRepositoryInterface.php
│   │   │   │   ├── PhotoRepositoryInterface.php
│   │   │   │   ├── BoardPostRepositoryInterface.php
│   │   │   │   ├── PackingItemRepositoryInterface.php
│   │   │   │   └── ExpenseRepositoryInterface.php
│   │   │   └── Exceptions/          #   ドメイン例外
│   │   │       └── DomainException.php
│   │   │
│   │   └── Application/
│   │       ├── UseCases/            #   ユースケース
│   │       │   ├── Spot/
│   │       │   │   ├── GetSpotListUseCase.php
│   │       │   │   ├── GetSpotDetailUseCase.php
│   │       │   │   └── CreateSpotMemoUseCase.php
│   │       │   ├── Album/
│   │       │   │   ├── UploadPhotoUseCase.php
│   │       │   │   └── GetAlbumUseCase.php
│   │       │   ├── Board/
│   │       │   │   ├── GetBoardPostsUseCase.php
│   │       │   │   ├── PostMessageUseCase.php
│   │       │   │   └── AddReactionUseCase.php
│   │       │   ├── Itinerary/
│   │       │   │   ├── GetItineraryUseCase.php
│   │       │   │   ├── CreateItineraryItemUseCase.php
│   │       │   │   ├── UpdateItineraryItemUseCase.php
│   │       │   │   ├── DeleteItineraryItemUseCase.php
│   │       │   │   └── ReorderItineraryUseCase.php
│   │       │   ├── Packing/
│   │       │   │   ├── GetPackingListUseCase.php
│   │       │   │   ├── CreatePackingItemUseCase.php
│   │       │   │   ├── UpdatePackingItemUseCase.php
│   │       │   │   └── DeletePackingItemUseCase.php
│   │       │   └── Expense/
│   │       │       ├── RecordExpenseUseCase.php
│   │       │       └── GetExpenseSummaryUseCase.php
│   │       └── DTOs/                #   データ転送オブジェクト
│   │           ├── SpotDTO.php
│   │           ├── PhotoDTO.php
│   │           ├── ItineraryItemDTO.php
│   │           ├── BoardPostDTO.php
│   │           ├── PackingItemDTO.php
│   │           └── ExpenseDTO.php
│   │
│   ├── app/                         # Infrastructure & Presentation
│   │   ├── Http/
│   │   │   ├── Controllers/Api/     #   Presentation: API コントローラー
│   │   │   │   ├── SpotController.php
│   │   │   │   ├── AlbumController.php
│   │   │   │   ├── BoardController.php
│   │   │   │   ├── ItineraryController.php
│   │   │   │   ├── PackingController.php
│   │   │   │   ├── ExpenseController.php
│   │   │   │   └── ExportController.php
│   │   │   └── Requests/            #   バリデーション
│   │   ├── Models/                  #   Infrastructure: Eloquent モデル
│   │   │   ├── User.php
│   │   │   ├── Spot.php
│   │   │   ├── Photo.php
│   │   │   └── ...
│   │   ├── Repositories/           #   Infrastructure: リポジトリ実装
│   │   │   ├── EloquentSpotRepository.php
│   │   │   ├── EloquentPhotoRepository.php
│   │   │   └── ...
│   │   └── Providers/
│   │       └── AppServiceProvider.php  # Interface → 実装のバインド
│   │
│   ├── database/
│   │   ├── migrations/              # マイグレーション
│   │   ├── seeders/                 # 初期データ（スポット情報等）
│   │   └── database.sqlite          # SQLite ファイル
│   ├── routes/
│   │   └── api.php                  # API ルート定義
│   ├── composer.json
│   └── .env
│
├── docker-compose.yml               # 本番用
├── .github/
│   └── workflows/
│       └── deploy.yml               # GitHub Actions CI/CD
├── ise_trip_app_plan.md             # 企画書
└── CONTRIBUTING.md                  # このファイル
```

#### バインド例（AppServiceProvider）

```php
// backend/app/Providers/AppServiceProvider.php
use Packages\Domain\Repositories\SpotRepositoryInterface;
use App\Repositories\EloquentSpotRepository;

public function register(): void
{
    $this->app->bind(SpotRepositoryInterface::class, EloquentSpotRepository::class);
    $this->app->bind(PhotoRepositoryInterface::class, EloquentPhotoRepository::class);
    // ...
}
```

#### データの流れ（例: スポット一覧取得）

```
[Nuxt] useFetch('/api/spots')
  → [Controller] SpotController@index
    → [UseCase] GetSpotListUseCase@execute()
      → [Repository Interface] SpotRepositoryInterface@findAll()
        → [実装] EloquentSpotRepository@findAll()  ← DI で注入
          → [Eloquent] Spot::all()
            → [SQLite]
```

---

## 開発環境（Dev Containers）

### コンテナ構成

```yaml
# .devcontainer/docker-compose.yml
services:
  app:                    # メイン開発コンテナ
    build: .
    image: php:8.4-cli    # PHP 8.4 + Node.js 22 同居
    volumes:
      - ..:/workspace
    ports:
      - "3900:3900"       # Nuxt dev server
      - "8900:8900"       # Laravel dev server
```

### devcontainer.json の方針
- **ベースイメージ**: PHP 8.4 + Node.js 22（1コンテナで両方動かす）
- **VS Code 拡張**: Vue - Official, PHP Intelephense, Tailwind CSS IntelliSense, SQLite Viewer
- **postCreateCommand**: `cd backend && composer install && cd ../frontend && npm install`
- **forwardPorts**: 3900 (Nuxt), 8900 (Laravel)

### ローカル開発コマンド

```bash
# Dev Container 内で実行

# バックエンド起動
cd backend
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate --seed
php artisan serve --host=0.0.0.0

# フロントエンド起動（別ターミナル）
cd frontend
npm install
npm run dev
```

---

## インフラ構成

### Google Cloud Run

- **サービス数**: 2（Nuxt用 + Laravel用）または 1（まとめる場合）
- **最小インスタンス**: 0（コスト0運用、コールドスタートあり）
- **最大インスタンス**: 1（2人しか使わない）
- **メモリ**: 512MB
- **リージョン**: asia-northeast1 (東京)

### SQLite の永続化

Cloud Run のコンテナは揮発するため、SQLite を永続化する方法：
- **Cloud Run Volume Mounts + Cloud Storage FUSE** でマウント
- または **Litestream** で SQLite を Cloud Storage にリアルタイム複製

### Cloudflare R2（写真ストレージ）

- S3互換 API → Laravel の `Storage::disk('s3')` でそのまま使える
- 無料枠: 10GB ストレージ / 月1000万リクエスト
- エグレス（ダウンロード）完全無料

### GitHub Actions CI/CD パイプライン

```yaml
# .github/workflows/deploy.yml の概要
trigger: push to main

jobs:
  deploy:
    steps:
      - npm install & npm run build     # Nuxt 4 ビルド
      - composer install --no-dev       # Laravel 依存解決
      - Docker build                     # コンテナイメージ作成
      - Push to Artifact Registry        # GCR にプッシュ
      - Deploy to Cloud Run              # デプロイ
```

---

## DB 設計（主要テーブル）

```
users           # ユーザー（2人分）
spots           # 観光スポット情報
spot_memos      # スポットへの「行きたい！」メモ
itinerary_items # しおり項目（時間・スポット・メモ）
photos          # 写真メタデータ（R2のパス、スポット紐付け）
board_posts     # 掲示板投稿
reactions       # リアクション（スタンプ）
packing_items   # 持ち物リスト
expenses        # 費用記録
```

---

## Nuxt 4 の学習ポイント

| 機能                           | 説明                                          | 使いどころ           |
| ------------------------------ | --------------------------------------------- | -------------------- |
| **ファイルベースルーティング** | `app/pages/` にファイルを置くだけでルート生成 | 全ページ             |
| **useFetch / $fetch**          | SSR対応のデータフェッチ                       | Laravel API 呼び出し |
| **useState**                   | SSR対応のグローバルステート                   | ユーザー情報保持     |
| **Nitro サーバー**             | `server/api/` でBFFレイヤー構築可能           | API Proxy、認証中継  |
| **useHead / useSeoMeta**       | メタタグ動的設定                              | 各ページのタイトル   |
| **ミドルウェア**               | `middleware/` でルートガード                  | 認証チェック         |
| **Nuxt UI / Nuxt Image**       | 公式モジュールで UI 強化                      | 画像最適化、UI部品   |

---

## 開発ルール

### ブランチ戦略
- `main`: 本番デプロイ対象
- `feature/*`: 機能開発ブランチ
- main への直プッシュOK（2人専用アプリのため）

### コーディング規約
- **PHP**: PSR-12 準拠（Laravel Pint で自動フォーマット）
- **Vue/TS**: ESLint + Prettier（Nuxt ESLint Module）
- **コミットメッセージ**: 日本語OK、`feat:` `fix:` `docs:` プレフィックス推奨

---

## 無料枠まとめ

| サービス          | 無料枠               | 用途                       |
| ----------------- | -------------------- | -------------------------- |
| Cloud Run         | 200万リクエスト/月   | アプリホスティング         |
| Artifact Registry | 500MB                | コンテナイメージ保存       |
| Cloud Storage     | 5GB                  | SQLite 複製先 (Litestream) |
| Cloudflare R2     | 10GB + エグレス無料  | 写真ストレージ             |
| GitHub Actions    | 2,000分/月 (private) | CI/CD                      |

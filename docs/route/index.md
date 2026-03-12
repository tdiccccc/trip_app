# フロントエンド ルート定義書

## 概要

伊勢旅行メモリーアプリのフロントエンド（Nuxt 4）ルート定義。
Nuxt 4 のファイルベースルーティング（`frontend/app/pages/`）に基づく。
複数旅行管理に対応し、旅行関連のページは `/trips/{tripId}/...` スコープで管理される。

本書は実装コードの実態に基づいて記載している（最終更新: 2026-03-11）。

---

## ルート一覧

| # | ルートパス | ページファイル | 画面名 | 認証 | レイアウト |
|---|-----------|--------------|--------|------|-----------|
| 1 | `/` | `pages/index.vue` | リダイレクト | 要 | - |
| 2 | `/login` | `pages/login.vue` | ログイン | 不要 | `blank` |
| 3 | `/trips` | `pages/trips/index.vue` | 旅行一覧 | 要 | `default` |
| 4 | `/trips/new` | `pages/trips/new.vue` | 旅行作成 | 要 | `default` |
| 5 | `/trips/:tripId` | `pages/trips/[tripId]/index.vue` | 旅行トップ | 要 | `default` |
| 6 | `/trips/:tripId/itinerary` | `pages/trips/[tripId]/itinerary.vue` | しおり | 要 | `default` |
| 7 | `/trips/:tripId/spots/:id` | `pages/trips/[tripId]/spots/[id].vue` | スポット詳細 | 要 | `default` |
| 8 | `/trips/:tripId/album` | `pages/trips/[tripId]/album/index.vue` | 写真アルバム一覧 | 要 | `default` |
| 9 | `/trips/:tripId/album/slideshow` | `pages/trips/[tripId]/album/slideshow.vue` | スライドショー再生 | 要 | `fullscreen` |
| 10 | `/trips/:tripId/board` | `pages/trips/[tripId]/board.vue` | ふたりの掲示板 | 要 | `default` |
| 11 | `/trips/:tripId/packing` | `pages/trips/[tripId]/packing.vue` | パッキングリスト | 要 | `default` |
| 12 | `/trips/:tripId/expenses` | `pages/trips/[tripId]/expenses.vue` | 費用メモ | 要 | `default` |
| 13 | `/trips/:tripId/export` | `pages/trips/[tripId]/export.vue` | エクスポート | 要 | `default` |

---

## ルート詳細

### ルート1: /

| 項目 | 内容 |
|------|------|
| ファイル | `frontend/app/pages/index.vue` |
| 認証 | 要（`middleware: ['auth']`） |
| レイアウト | - |
| 使用コンポーネント | なし |
| 使用 Composable | なし |
| 使用 API | なし |

主要機能:
- ログイン後 `/trips` にリダイレクト

---

### ルート2: /login

| 項目 | 内容 |
|------|------|
| ファイル | `frontend/app/pages/login.vue` |
| 認証 | 不要（未認証ユーザー向け、認証済みなら `/trips` へリダイレクト） |
| レイアウト | blank（ヘッダー・ナビなし） |
| 使用コンポーネント | ページ内実装（フォームはインライン） |
| 使用 Composable | `useAuth` |
| 使用 API | `GET /sanctum/csrf-cookie`, `POST /api/login` |

主要機能:
- メールアドレス + パスワードによるログインフォーム
- Sanctum SPA 認証（CSRF Cookie 取得 → ログイン）
- エラーメッセージ表示
- ローディング状態の制御

---

### ルート3: /trips

| 項目 | 内容 |
|------|------|
| ファイル | `frontend/app/pages/trips/index.vue` |
| 認証 | 要（`middleware: ['auth']`） |
| レイアウト | default |
| 使用コンポーネント | `TripCard` |
| 使用 Composable | `useTrips` |
| 使用 API | `GET /api/trips` |

主要機能:
- 参加している旅行の一覧表示（カード形式）
- 旅行作成ページへの導線
- 旅行カード（タイトル、日程、行き先、メンバー表示）
- 旅行タップで旅行トップページへ遷移

---

### ルート4: /trips/new

| 項目 | 内容 |
|------|------|
| ファイル | `frontend/app/pages/trips/new.vue` |
| 認証 | 要（`middleware: ['auth']`） |
| レイアウト | default |
| 使用コンポーネント | ページ内実装（フォームはインライン） |
| 使用 Composable | `useTrips` |
| 使用 API | `POST /api/trips` |

主要機能:
- 旅行新規作成フォーム（タイトル、説明、行き先、開始日、終了日）
- メンバー追加（ユーザー選択）
- 作成後は旅行トップページへ遷移

---

### ルート5: /trips/:tripId

| 項目 | 内容 |
|------|------|
| ファイル | `frontend/app/pages/trips/[tripId]/index.vue` |
| 認証 | 要（`middleware: ['auth']`） |
| レイアウト | default |
| 使用コンポーネント | ページ内実装（`NuxtLink` のみ使用） |
| 使用 Composable | `useTrips` |
| 使用 API | `GET /api/trips/:tripId` |

主要機能:
- 旅行開始日までのカウントダウンタイマー（日・時・分・秒）
- 旅行開始後のメッセージ表示
- クイックリンク（しおり、アルバム、掲示板、パッキング、費用メモ、エクスポートへの導線。2列グリッドのカード形式）
- 旅行情報表示（タイトル、日程、行き先、メンバー）
- ~~カバー画像表示~~（将来対応）
- ~~天気予報ウィジェット~~（将来対応）

---

### ルート6: /trips/:tripId/itinerary

| 項目 | 内容 |
|------|------|
| ファイル | `frontend/app/pages/trips/[tripId]/itinerary.vue` |
| 認証 | 要 |
| レイアウト | default |
| 使用コンポーネント | `TimelineItem`, `TimelineForm` |
| 使用 Composable | `useItinerary` |
| 使用 API | `GET /api/trips/:tripId/itinerary`, `POST /api/trips/:tripId/itinerary`, `PATCH /api/trips/:tripId/itinerary/:id`, `DELETE /api/trips/:tripId/itinerary/:id` |

主要機能:
- 日付タブ切り替え（旅行日程に基づく）
- タイムライン形式で行程一覧表示（sort_order 順）
- 行程の追加・編集・削除（モーダルフォーム）
- 「今ここ」ハイライト（当日の現在時刻に該当する行程を強調）
- ~~並び替え（ドラッグ＆ドロップ）~~（`reorderItems` は composable に実装済みだが UI 未実装）

---

### ルート7: /trips/:tripId/spots/:id

| 項目 | 内容 |
|------|------|
| ファイル | `frontend/app/pages/trips/[tripId]/spots/[id].vue` |
| 認証 | 要 |
| レイアウト | default |
| 使用コンポーネント | `SpotMemoItem`, `PhotoGrid` |
| 使用 Composable | `useSpots` |
| 使用 API | `GET /api/trips/:tripId/spots/:id`, `POST /api/trips/:tripId/spots/:id/memos` |

主要機能:
- スポット情報表示（ヒーロー画像、カテゴリ、名前、説明文）
- 詳細情報カード（住所、営業時間、料金情報）
- Google Maps リンク（外部リンクとして実装）
- スポット関連写真の一覧表示（PhotoGrid 使用）
- メモの一覧表示・追加（SpotMemoItem 使用）

---

### ルート8: /trips/:tripId/album

| 項目 | 内容 |
|------|------|
| ファイル | `frontend/app/pages/trips/[tripId]/album/index.vue` |
| 認証 | 要 |
| レイアウト | default |
| 使用コンポーネント | `PhotoGrid`, `PhotoUploader` |
| 使用 Composable | `useAlbum`, `useSpots` |
| 使用 API | `GET /api/trips/:tripId/photos`, `POST /api/trips/:tripId/photos`, `DELETE /api/trips/:tripId/photos/:id`, `GET /api/trips/:tripId/spots` |

主要機能:
- 写真のグリッド表示（PhotoGrid）
- 写真アップロード（PhotoUploader）
- スポット別フィルタ（select ドロップダウン）
- タップで拡大表示（インライン Lightbox 実装）
- 拡大表示からの削除

---

### ルート9: /trips/:tripId/album/slideshow

| 項目 | 内容 |
|------|------|
| ファイル | `frontend/app/pages/trips/[tripId]/album/slideshow.vue` |
| 認証 | 要 |
| レイアウト | fullscreen（全画面、ナビ非表示） |
| 使用コンポーネント | `SlideshowPlayer`, `SlideshowControls` |
| 使用 Composable | `useSlideshow`, `useAlbum` |
| 使用 API | `GET /api/trips/:tripId/photos` |

主要機能:
- 写真の自動再生スライドショー（撮影日時順）
- トランジションエフェクト切り替え（フェード / スライド）
- 再生速度調整
- 再生 / 一時停止 / 前へ / 次へ
- コントロール自動非表示（3秒後）・タップで再表示
- ~~BGM 再生~~（将来対応）

---

### ルート10: /trips/:tripId/board

| 項目 | 内容 |
|------|------|
| ファイル | `frontend/app/pages/trips/[tripId]/board.vue` |
| 認証 | 要 |
| レイアウト | default |
| 使用コンポーネント | ページ内実装（全てインライン） |
| 使用 Composable | `useBoard`, `useAuth` |
| 使用 API | `GET /api/trips/:tripId/board`, `POST /api/trips/:tripId/board`, `POST /api/trips/:tripId/board/:id/reactions` |

主要機能:
- メッセージ投稿（テキスト入力、画面下部固定フォーム）
- 投稿一覧表示（自分の投稿は右寄せ、相手は左寄せ）
- 絵文字リアクション（6種類のプリセット絵文字）
- リアクション数の集計・表示
- ~~「今日のベストショット」共有~~（将来対応）

---

### ルート11: /trips/:tripId/packing

| 項目 | 内容 |
|------|------|
| ファイル | `frontend/app/pages/trips/[tripId]/packing.vue` |
| 認証 | 要 |
| レイアウト | default |
| 使用コンポーネント | ページ内実装（全てインライン） |
| 使用 Composable | `usePacking` |
| 使用 API | `GET /api/trips/:tripId/packing`, `POST /api/trips/:tripId/packing`, `PATCH /api/trips/:tripId/packing/:id`, `DELETE /api/trips/:tripId/packing/:id` |

主要機能:
- チェックボックス形式の持ち物リスト
- 担当者フィルタ（全て / たろう / はなこ）
- 準備状況プログレスバー（チェック済み / 合計）
- アイテムの追加（名前 + 担当者）・削除
- チェック状態のトグル
- ~~テンプレートから一括追加~~（将来対応）

---

### ルート12: /trips/:tripId/expenses

| 項目 | 内容 |
|------|------|
| ファイル | `frontend/app/pages/trips/[tripId]/expenses.vue` |
| 認証 | 要 |
| レイアウト | default |
| 使用コンポーネント | ページ内実装（全てインライン） |
| 使用 Composable | `useExpenses` |
| 使用 API | `GET /api/trips/:tripId/expenses`, `POST /api/trips/:tripId/expenses`, `DELETE /api/trips/:tripId/expenses/:id`, `GET /api/trips/:tripId/expenses/summary` |

主要機能:
- 支出記録の追加（項目名・金額・カテゴリ・支払者・メモ）
- 支出一覧表示（カテゴリバッジ・支払者・日時・メモ付き）
- カテゴリ別フィルタ（食事・交通・チケット・お土産・宿泊・その他）
- サマリーカード（合計金額・一人あたり・カテゴリ別内訳）
- 支出の削除

---

### ルート13: /trips/:tripId/export

| 項目 | 内容 |
|------|------|
| ファイル | `frontend/app/pages/trips/[tripId]/export.vue` |
| 認証 | 要 |
| レイアウト | default |
| 使用コンポーネント | なし |
| 使用 Composable | なし |
| 使用 API | なし（フロントエンドはスタブ状態） |

主要機能:
- **現状スタブ**: タイトルと説明文のみ表示
- ~~PDF しおり出力~~（バックエンド API は実装済み、フロントエンド未実装）
- ~~フォトブック風 PDF 生成~~（バックエンド API は実装済み、フロントエンド未実装）
- ~~スライドショー動画（MP4）書き出し~~（将来対応）
- ~~ZIP 一括ダウンロード~~（バックエンド API は実装済み、フロントエンド未実装）

---

## 認証ミドルウェア

認証が必要なルートは Nuxt ミドルウェア `auth` で保護する。

```
frontend/app/middleware/auth.ts
```

- 未認証ユーザーが認証必須ページにアクセスした場合、`/login` にリダイレクト
- `/login` は未認証でもアクセス可能
- `/` は認証後 `/trips` にリダイレクト
- `/trips` がログイン後のランディングページ

---

## レイアウト構成

| レイアウト名 | ファイル | 用途 |
|-------------|---------|------|
| `default` | `layouts/default.vue` | 共通ヘッダー + ボトムナビ付きレイアウト |
| `blank` | `layouts/blank.vue` | ヘッダー・ナビなしのシンプルレイアウト（ログイン画面） |
| `fullscreen` | `layouts/fullscreen.vue` | 全画面レイアウト（スライドショー） |

---

## 実装済みコンポーネント一覧

| コンポーネント名 | ファイル | 使用ページ |
|-----------------|---------|-----------|
| `TripCard` | `components/TripCard.vue` | `/trips` |
| `TimelineItem` | `components/TimelineItem.vue` | `/trips/:tripId/itinerary` |
| `TimelineForm` | `components/TimelineForm.vue` | `/trips/:tripId/itinerary` |
| `TransportIcon` | `components/TransportIcon.vue` | （TimelineItem 内で使用） |
| `SpotCard` | `components/SpotCard.vue` | （スポット一覧用、現在ページからの直接使用なし） |
| `SpotMemoItem` | `components/SpotMemoItem.vue` | `/trips/:tripId/spots/:id` |
| `PhotoGrid` | `components/PhotoGrid.vue` | `/trips/:tripId/album`, `/trips/:tripId/spots/:id` |
| `PhotoUploader` | `components/PhotoUploader.vue` | `/trips/:tripId/album` |
| `SlideshowPlayer` | `components/SlideshowPlayer.vue` | `/trips/:tripId/album/slideshow` |
| `SlideshowControls` | `components/SlideshowControls.vue` | `/trips/:tripId/album/slideshow` |

---

## ファイルツリー（pages/）

```
frontend/app/pages/
├── index.vue                          # / -> /trips にリダイレクト
├── login.vue                          # /login
└── trips/
    ├── index.vue                      # /trips (旅行一覧)
    ├── new.vue                        # /trips/new (旅行作成)
    └── [tripId]/
        ├── index.vue                  # /trips/:tripId
        ├── itinerary.vue              # /trips/:tripId/itinerary
        ├── spots/
        │   └── [id].vue              # /trips/:tripId/spots/:id
        ├── album/
        │   ├── index.vue             # /trips/:tripId/album
        │   └── slideshow.vue         # /trips/:tripId/album/slideshow
        ├── board.vue                  # /trips/:tripId/board
        ├── packing.vue                # /trips/:tripId/packing
        ├── expenses.vue               # /trips/:tripId/expenses
        └── export.vue                 # /trips/:tripId/export
```

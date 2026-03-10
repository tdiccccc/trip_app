# フロントエンド ルート定義書

## 概要

伊勢旅行アプリのフロントエンド（Nuxt 4）ルート定義。
Nuxt 4 のファイルベースルーティング（`frontend/app/pages/`）に基づく。

---

## ルート一覧

| # | ルートパス | ページファイル | 画面名 | 認証 | レイアウト |
|---|-----------|--------------|--------|------|-----------|
| 1 | `/` | `pages/index.vue` | トップ | 不要 | `default` |
| 2 | `/login` | `pages/login.vue` | ログイン | 不要 | `blank` |
| 3 | `/itinerary` | `pages/itinerary.vue` | しおり | 要 | `default` |
| 4 | `/spots/:id` | `pages/spots/[id].vue` | スポット詳細 | 要 | `default` |
| 5 | `/album` | `pages/album/index.vue` | 写真アルバム一覧 | 要 | `default` |
| 6 | `/album/slideshow` | `pages/album/slideshow.vue` | スライドショー再生 | 要 | `fullscreen` |
| 7 | `/board` | `pages/board.vue` | ふたりの掲示板 | 要 | `default` |
| 8 | `/packing` | `pages/packing.vue` | パッキングリスト | 要 | `default` |
| 9 | `/expenses` | `pages/expenses.vue` | 費用メモ | 要 | `default` |
| 10 | `/export` | `pages/export.vue` | エクスポート | 要 | `default` |

---

## ルート詳細

### 1. `/` — トップ（カウントダウン + カバー画像）

| 項目 | 内容 |
|------|------|
| **ファイル** | `frontend/app/pages/index.vue` |
| **認証** | 不要 |
| **レイアウト** | `default` |
| **概要** | 旅行当日までのカウントダウンタイマー、カバー画像、天気予報を表示するランディングページ |
| **主要機能** | カウントダウン表示、カバー画像表示、天気予報ウィジェット |
| **主要コンポーネント** | `CountdownTimer`, `CoverImage`, `WeatherWidget` |
| **主要 composables** | `useCountdown` |
| **呼び出し API** | なし（静的表示 or 天気予報外部API） |

---

### 2. `/login` — ログイン

| 項目 | 内容 |
|------|------|
| **ファイル** | `frontend/app/pages/login.vue` |
| **認証** | 不要（未認証ユーザー向け） |
| **レイアウト** | `blank`（ヘッダー・ナビなし） |
| **概要** | Sanctum SPA 認証によるログイン画面 |
| **主要機能** | メールアドレス + パスワードでログイン |
| **主要コンポーネント** | `LoginForm` |
| **主要 composables** | `useAuth` |
| **呼び出し API** | `GET /sanctum/csrf-cookie`, `POST /api/login` |

---

### 3. `/itinerary` — しおり（タイムライン）

| 項目 | 内容 |
|------|------|
| **ファイル** | `frontend/app/pages/itinerary.vue` |
| **認証** | 要 |
| **レイアウト** | `default` |
| **概要** | 旅行の行程を時間軸タイムラインで表示・編集。ユーザーが自由に行程を追加・編集・削除できる。当日は現在時刻に自動ハイライト |
| **主要機能** | タイムライン表示、行程の追加・編集・削除・並び替え、「今ここ」ハイライト、移動手段アイコン、スポットへのリンク |
| **主要コンポーネント** | `Timeline`, `TimelineItem`, `TimelineForm`, `TransportIcon`, `SortHandle` |
| **主要 composables** | `useItinerary`, `useCurrentTime` |
| **呼び出し API** | `GET /api/itinerary`, `POST /api/itinerary`, `PATCH /api/itinerary/:id`, `DELETE /api/itinerary/:id`, `PATCH /api/itinerary/reorder` |

---

### 4. `/spots/:id` — スポット詳細

| 項目 | 内容 |
|------|------|
| **ファイル** | `frontend/app/pages/spots/[id].vue` |
| **認証** | 要 |
| **レイアウト** | `default` |
| **概要** | 各観光スポットの基本情報、Google Maps リンク、メモ機能を提供 |
| **主要機能** | スポット情報表示（住所・営業時間・料金）、Google Maps ナビリンク、「行きたい！」メモの追加・編集 |
| **主要コンポーネント** | `SpotInfo`, `SpotMemo`, `GoogleMapsLink`, `SpotPhotoGallery` |
| **主要 composables** | `useSpot` |
| **呼び出し API** | `GET /api/spots/:id`, `POST /api/spots/:id/memos`, `GET /api/spots/:id/photos` |

---

### 5. `/album` — 写真アルバム一覧

| 項目 | 内容 |
|------|------|
| **ファイル** | `frontend/app/pages/album/index.vue` |
| **認証** | 要 |
| **レイアウト** | `default` |
| **概要** | アップロード済み写真のサムネイル一覧。スポット別・時系列で自動整理 |
| **主要機能** | サムネイル一覧表示、写真アップロード、タップで拡大表示、スポット別フィルタ |
| **主要コンポーネント** | `PhotoGrid`, `PhotoUploader`, `PhotoLightbox`, `AlbumFilter` |
| **主要 composables** | `useAlbum`, `usePhotoUpload` |
| **呼び出し API** | `GET /api/photos`, `POST /api/photos`, `DELETE /api/photos/:id` |

---

### 6. `/album/slideshow` — スライドショー再生

| 項目 | 内容 |
|------|------|
| **ファイル** | `frontend/app/pages/album/slideshow.vue` |
| **認証** | 要 |
| **レイアウト** | `fullscreen`（全画面、ナビ非表示） |
| **概要** | アルバム写真を BGM 付きスライドショーで自動再生 |
| **主要機能** | 自動再生、トランジションエフェクト（フェード・スライド）、再生速度調整、BGM 再生 |
| **主要コンポーネント** | `SlideshowPlayer`, `SlideshowControls` |
| **主要 composables** | `useSlideshow`, `useAlbum` |
| **呼び出し API** | `GET /api/photos` |

---

### 7. `/board` — ふたりの掲示板

| 項目 | 内容 |
|------|------|
| **ファイル** | `frontend/app/pages/board.vue` |
| **認証** | 要 |
| **レイアウト** | `default` |
| **概要** | 旅行中にリアルタイムで感想・ひとことを投稿し合う掲示板 |
| **主要機能** | メッセージ投稿、スタンプ / リアクション、「今日のベストショット」共有 |
| **主要コンポーネント** | `BoardFeed`, `BoardPostForm`, `ReactionPicker`, `BestShotCard` |
| **主要 composables** | `useBoard` |
| **呼び出し API** | `GET /api/board`, `POST /api/board`, `POST /api/board/:id/reactions` |

---

### 8. `/packing` — パッキングリスト

| 項目 | 内容 |
|------|------|
| **ファイル** | `frontend/app/pages/packing.vue` |
| **認証** | 要 |
| **レイアウト** | `default` |
| **概要** | 共有持ち物チェックリスト。担当分けとテンプレート一括追加に対応 |
| **主要機能** | チェックボックス形式の持ち物リスト、担当分け（自分 / 彼女 / 共有）、テンプレートから一括追加 |
| **主要コンポーネント** | `PackingList`, `PackingItem`, `PackingTemplateSelector` |
| **主要 composables** | `usePacking` |
| **呼び出し API** | `GET /api/packing`, `POST /api/packing`, `PATCH /api/packing/:id`, `DELETE /api/packing/:id` |

---

### 9. `/expenses` — 費用メモ

| 項目 | 内容 |
|------|------|
| **ファイル** | `frontend/app/pages/expenses.vue` |
| **認証** | 要 |
| **レイアウト** | `default` |
| **概要** | 旅行の支出記録と自動割り勘計算 |
| **主要機能** | 支出記録（誰が・何に・いくら）、合計 & 割り勘自動計算、カテゴリ別集計（交通費・食事・お土産・宿泊） |
| **主要コンポーネント** | `ExpenseList`, `ExpenseForm`, `ExpenseSummary`, `SplitBillCard` |
| **主要 composables** | `useExpenses` |
| **呼び出し API** | `GET /api/expenses`, `POST /api/expenses`, `DELETE /api/expenses/:id`, `GET /api/expenses/summary` |

---

### 10. `/export` — エクスポート

| 項目 | 内容 |
|------|------|
| **ファイル** | `frontend/app/pages/export.vue` |
| **認証** | 要 |
| **レイアウト** | `default` |
| **概要** | 旅の記録を各種形式でエクスポート |
| **主要機能** | PDF しおり出力、フォトブック風 PDF 生成、スライドショー動画（MP4）書き出し、ZIP 一括ダウンロード |
| **主要コンポーネント** | `ExportOptionCard`, `ExportProgress` |
| **主要 composables** | `useExport` |
| **呼び出し API** | `POST /api/export/itinerary-pdf`, `POST /api/export/photobook-pdf`, `POST /api/export/slideshow-video`, `POST /api/export/zip` |

---

## 認証ミドルウェア

認証が必要なルートは Nuxt ミドルウェア `auth` で保護する。

```
frontend/app/middleware/auth.ts
```

- 未認証ユーザーが認証必須ページにアクセスした場合、`/login` にリダイレクト
- `/` と `/login` は未認証でもアクセス可能

---

## レイアウト構成

| レイアウト名 | ファイル | 用途 |
|-------------|---------|------|
| `default` | `layouts/default.vue` | 共通ヘッダー + ボトムナビ付きレイアウト |
| `blank` | `layouts/blank.vue` | ヘッダー・ナビなしのシンプルレイアウト（ログイン画面） |
| `fullscreen` | `layouts/fullscreen.vue` | 全画面レイアウト（スライドショー） |

---

## ファイルツリー（pages/）

```
frontend/app/pages/
├── index.vue              # /
├── login.vue              # /login
├── itinerary.vue          # /itinerary
├── spots/
│   └── [id].vue           # /spots/:id
├── album/
│   ├── index.vue          # /album
│   └── slideshow.vue      # /album/slideshow
├── board.vue              # /board
├── packing.vue            # /packing
├── expenses.vue           # /expenses
└── export.vue             # /export
```

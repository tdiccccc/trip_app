# API エンドポイント仕様書

## 概要

伊勢旅行アプリの REST API 仕様書。
Laravel 12 が `/api/*` エンドポイントを提供し、フロントエンド（Nuxt 4）から呼び出される。

---

## 共通仕様

### ベース URL

```
/api
```

### 認証方式

Laravel Sanctum の SPA 認証（Cookie ベース）を使用する。

- ログイン前に `GET /sanctum/csrf-cookie` で CSRF トークンを取得する
- 以降のリクエストには Cookie が自動付与される（同一ドメイン運用）
- 認証が必要なエンドポイントには `auth:sanctum` ミドルウェアが適用される

### 共通リクエストヘッダー

| ヘッダー | 値 | 説明 |
|---------|-----|------|
| `Accept` | `application/json` | JSON レスポンスを要求 |
| `Content-Type` | `application/json` | リクエストボディが JSON の場合 |
| `Content-Type` | `multipart/form-data` | ファイルアップロード時 |
| `X-XSRF-TOKEN` | `{token}` | CSRF トークン（Cookie から自動取得） |
| `Referer` | `{フロントエンドURL}` | Sanctum SPA 認証に必要 |

### 共通レスポンス形式

#### 成功時（単一リソース）

```json
{
  "data": {
    "id": 1,
    "name": "..."
  }
}
```

#### 成功時（リスト）

```json
{
  "data": [
    { "id": 1, "name": "..." },
    { "id": 2, "name": "..." }
  ]
}
```

#### 成功時（ページネーション付きリスト）

```json
{
  "data": [...],
  "meta": {
    "current_page": 1,
    "last_page": 3,
    "per_page": 15,
    "total": 42
  },
  "links": {
    "first": "/api/resource?page=1",
    "last": "/api/resource?page=3",
    "prev": null,
    "next": "/api/resource?page=2"
  }
}
```

#### エラー時（バリデーションエラー: 422）

```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field_name": [
      "The field_name is required."
    ]
  }
}
```

#### エラー時（認証エラー: 401）

```json
{
  "message": "Unauthenticated."
}
```

#### エラー時（リソース未発見: 404）

```json
{
  "message": "Not found."
}
```

#### エラー時（サーバーエラー: 500）

```json
{
  "message": "Server Error."
}
```

### 共通ステータスコード

| コード | 意味 | 使用場面 |
|--------|------|---------|
| 200 | OK | 取得・更新成功 |
| 201 | Created | リソース作成成功 |
| 204 | No Content | 削除成功 |
| 400 | Bad Request | 不正なリクエスト |
| 401 | Unauthorized | 未認証 |
| 403 | Forbidden | 権限なし |
| 404 | Not Found | リソース未発見 |
| 422 | Unprocessable Entity | バリデーションエラー |
| 500 | Internal Server Error | サーバー内部エラー |

---

## 1. 認証（Auth）

### 1-1. CSRF Cookie 取得

CSRF トークンを Cookie にセットする。SPA 認証の前に必ず呼び出す。

| 項目 | 内容 |
|------|------|
| **メソッド** | `GET` |
| **パス** | `/sanctum/csrf-cookie` |
| **認証** | 不要 |
| **コントローラー** | Laravel Sanctum 標準 |

#### レスポンス

| コード | 説明 |
|--------|------|
| 204 | CSRF Cookie がセットされる（レスポンスボディなし） |

---

### 1-2. ログイン

メールアドレスとパスワードで認証する。

| 項目 | 内容 |
|------|------|
| **メソッド** | `POST` |
| **パス** | `/api/login` |
| **認証** | 不要 |
| **コントローラー** | `AuthController@login` |

#### リクエストボディ

| フィールド | 型 | 必須 | バリデーション | 説明 |
|-----------|-----|------|-------------|------|
| email | string | YES | required, email | メールアドレス |
| password | string | YES | required, string | パスワード |

```json
{
  "email": "user@example.com",
  "password": "password123"
}
```

#### レスポンス

**成功（200）**

```json
{
  "data": {
    "id": 1,
    "name": "たろう",
    "email": "user@example.com"
  }
}
```

**エラー（422）** -- 認証失敗

```json
{
  "message": "The given data was invalid.",
  "errors": {
    "email": [
      "メールアドレスまたはパスワードが正しくありません。"
    ]
  }
}
```

---

### 1-3. ログアウト

現在のセッションを無効化する。

| 項目 | 内容 |
|------|------|
| **メソッド** | `POST` |
| **パス** | `/api/logout` |
| **認証** | 要 |
| **コントローラー** | `AuthController@logout` |

#### レスポンス

| コード | 説明 |
|--------|------|
| 204 | ログアウト成功（レスポンスボディなし） |

---

### 1-4. 認証ユーザー取得

現在ログイン中のユーザー情報を取得する。

| 項目 | 内容 |
|------|------|
| **メソッド** | `GET` |
| **パス** | `/api/user` |
| **認証** | 要 |
| **コントローラー** | `AuthController@me` |

#### レスポンス

**成功（200）**

```json
{
  "data": {
    "id": 1,
    "name": "たろう",
    "email": "user@example.com"
  }
}
```

---

## 2. しおり（Itinerary）

### 2-1. しおり一覧取得

旅行行程のタイムライン一覧を日付・並び順で取得する。

| 項目 | 内容 |
|------|------|
| **メソッド** | `GET` |
| **パス** | `/api/itinerary` |
| **認証** | 要 |
| **コントローラー** | `ItineraryController@index` |
| **UseCase** | `GetItineraryUseCase` |

#### クエリパラメータ

| パラメータ | 型 | 必須 | 説明 |
|-----------|-----|------|------|
| date | string | NO | 日付フィルタ（YYYY-MM-DD）。省略時は全日程を返す |

#### レスポンス

**成功（200）**

```json
{
  "data": [
    {
      "id": 1,
      "user_id": 1,
      "spot_id": 1,
      "title": "伊勢神宮 内宮参拝",
      "memo": "朝早めに行くと空いている",
      "date": "2026-03-28",
      "start_time": "10:00",
      "end_time": "12:00",
      "transport": "train",
      "sort_order": 1,
      "spot": {
        "id": 1,
        "name": "伊勢神宮 内宮",
        "google_maps_url": "https://maps.google.com/..."
      },
      "user": {
        "id": 1,
        "name": "たろう"
      },
      "created_at": "2026-03-01T10:00:00.000000Z",
      "updated_at": "2026-03-01T10:00:00.000000Z"
    },
    {
      "id": 2,
      "user_id": 1,
      "spot_id": 2,
      "title": "おかげ横丁で食べ歩き",
      "memo": "赤福本店に寄る",
      "date": "2026-03-28",
      "start_time": "12:30",
      "end_time": "14:30",
      "transport": "walk",
      "sort_order": 2,
      "spot": {
        "id": 2,
        "name": "おかげ横丁",
        "google_maps_url": "https://maps.google.com/..."
      },
      "user": {
        "id": 1,
        "name": "たろう"
      },
      "created_at": "2026-03-01T10:00:00.000000Z",
      "updated_at": "2026-03-01T10:00:00.000000Z"
    }
  ]
}
```

---

### 2-2. しおり項目作成

新しい行程項目を追加する。

| 項目 | 内容 |
|------|------|
| **メソッド** | `POST` |
| **パス** | `/api/itinerary` |
| **認証** | 要 |
| **コントローラー** | `ItineraryController@store` |

#### リクエストボディ

| フィールド | 型 | 必須 | バリデーション | 説明 |
|-----------|-----|------|-------------|------|
| spot_id | integer | NO | nullable, exists:spots,id | スポットID |
| title | string | YES | required, max:255 | 項目タイトル |
| memo | string | NO | nullable, max:1000 | メモ |
| date | string | YES | required, date_format:Y-m-d | 日付 |
| start_time | string | NO | nullable, date_format:H:i | 開始時刻 |
| end_time | string | NO | nullable, date_format:H:i | 終了時刻 |
| transport | string | NO | nullable, in:train,car,walk,bus,none | 移動手段 |
| sort_order | integer | NO | nullable, integer, min:0 | 並び順 |

```json
{
  "spot_id": 1,
  "title": "伊勢神宮 内宮参拝",
  "memo": "朝早めに行くと空いている",
  "date": "2026-03-28",
  "start_time": "10:00",
  "end_time": "12:00",
  "transport": "train",
  "sort_order": 1
}
```

#### レスポンス

**成功（201）**

```json
{
  "data": {
    "id": 1,
    "user_id": 1,
    "spot_id": 1,
    "title": "伊勢神宮 内宮参拝",
    "memo": "朝早めに行くと空いている",
    "date": "2026-03-28",
    "start_time": "10:00",
    "end_time": "12:00",
    "transport": "train",
    "sort_order": 1,
    "spot": {
      "id": 1,
      "name": "伊勢神宮 内宮",
      "google_maps_url": "https://maps.google.com/..."
    },
    "user": {
      "id": 1,
      "name": "たろう"
    },
    "created_at": "2026-03-01T10:00:00.000000Z",
    "updated_at": "2026-03-01T10:00:00.000000Z"
  }
}
```

**エラー（422）**

```json
{
  "message": "The given data was invalid.",
  "errors": {
    "title": ["The title field is required."],
    "date": ["The date field is required."]
  }
}
```

---

### 2-3. しおり項目更新

既存の行程項目を更新する。

| 項目 | 内容 |
|------|------|
| **メソッド** | `PATCH` |
| **パス** | `/api/itinerary/{id}` |
| **認証** | 要 |
| **コントローラー** | `ItineraryController@update` |

#### パスパラメータ

| パラメータ | 型 | 説明 |
|-----------|-----|------|
| id | integer | しおり項目ID |

#### リクエストボディ

全フィールド任意。送信したフィールドのみ更新される。

| フィールド | 型 | 必須 | バリデーション | 説明 |
|-----------|-----|------|-------------|------|
| spot_id | integer | NO | nullable, exists:spots,id | スポットID |
| title | string | NO | sometimes, required, max:255 | 項目タイトル |
| memo | string | NO | nullable, max:1000 | メモ |
| date | string | NO | sometimes, required, date_format:Y-m-d | 日付 |
| start_time | string | NO | nullable, date_format:H:i | 開始時刻 |
| end_time | string | NO | nullable, date_format:H:i | 終了時刻 |
| transport | string | NO | nullable, in:train,car,walk,bus,none | 移動手段 |
| sort_order | integer | NO | nullable, integer, min:0 | 並び順 |

```json
{
  "title": "伊勢神宮 内宮参拝（御朱印あり）",
  "end_time": "12:30"
}
```

#### レスポンス

**成功（200）**

```json
{
  "data": {
    "id": 1,
    "user_id": 1,
    "spot_id": 1,
    "title": "伊勢神宮 内宮参拝（御朱印あり）",
    "memo": "朝早めに行くと空いている",
    "date": "2026-03-28",
    "start_time": "10:00",
    "end_time": "12:30",
    "transport": "train",
    "sort_order": 1,
    "spot": {
      "id": 1,
      "name": "伊勢神宮 内宮",
      "google_maps_url": "https://maps.google.com/..."
    },
    "user": {
      "id": 1,
      "name": "たろう"
    },
    "created_at": "2026-03-01T10:00:00.000000Z",
    "updated_at": "2026-03-15T14:30:00.000000Z"
  }
}
```

**エラー（404）**

```json
{
  "message": "Not found."
}
```

---

### 2-4. しおり項目削除

行程項目を削除する。

| 項目 | 内容 |
|------|------|
| **メソッド** | `DELETE` |
| **パス** | `/api/itinerary/{id}` |
| **認証** | 要 |
| **コントローラー** | `ItineraryController@destroy` |

#### パスパラメータ

| パラメータ | 型 | 説明 |
|-----------|-----|------|
| id | integer | しおり項目ID |

#### レスポンス

| コード | 説明 |
|--------|------|
| 204 | 削除成功（レスポンスボディなし） |
| 404 | 対象リソースが見つからない |

---

### 2-5. しおり並び替え

複数のしおり項目の sort_order を一括更新する。

| 項目 | 内容 |
|------|------|
| **メソッド** | `PATCH` |
| **パス** | `/api/itinerary/reorder` |
| **認証** | 要 |
| **コントローラー** | `ItineraryController@reorder` |

#### リクエストボディ

| フィールド | 型 | 必須 | バリデーション | 説明 |
|-----------|-----|------|-------------|------|
| items | array | YES | required, array, min:1 | 並び替え対象の配列 |
| items.*.id | integer | YES | required, exists:itinerary_items,id | しおり項目ID |
| items.*.sort_order | integer | YES | required, integer, min:0 | 新しい並び順 |

```json
{
  "items": [
    { "id": 1, "sort_order": 0 },
    { "id": 3, "sort_order": 1 },
    { "id": 2, "sort_order": 2 }
  ]
}
```

#### レスポンス

**成功（200）**

```json
{
  "message": "Reordered successfully."
}
```

---

## 3. スポット（Spot）

### 3-1. スポット一覧取得

観光スポットの一覧を取得する。Seeder で事前登録されたデータ。

| 項目 | 内容 |
|------|------|
| **メソッド** | `GET` |
| **パス** | `/api/spots` |
| **認証** | 要 |
| **コントローラー** | `SpotController@index` |
| **UseCase** | `GetSpotListUseCase` |

#### クエリパラメータ

| パラメータ | 型 | 必須 | 説明 |
|-----------|-----|------|------|
| category | string | NO | カテゴリフィルタ（sightseeing / food / hotel / other） |

#### レスポンス

**成功（200）**

```json
{
  "data": [
    {
      "id": 1,
      "name": "伊勢神宮 内宮",
      "description": "日本最高峰の神社。天照大御神を祀る。",
      "address": "三重県伊勢市宇治館町1",
      "latitude": 34.4554,
      "longitude": 136.7254,
      "business_hours": "5:00-18:00（季節により変動）",
      "price_info": "参拝無料",
      "google_maps_url": "https://maps.google.com/...",
      "image_url": "https://example.com/ise-naiku.jpg",
      "category": "sightseeing",
      "sort_order": 1,
      "created_at": "2026-03-01T00:00:00.000000Z",
      "updated_at": "2026-03-01T00:00:00.000000Z"
    }
  ]
}
```

---

### 3-2. スポット詳細取得

指定スポットの詳細情報をメモ・写真とともに取得する。

| 項目 | 内容 |
|------|------|
| **メソッド** | `GET` |
| **パス** | `/api/spots/{id}` |
| **認証** | 要 |
| **コントローラー** | `SpotController@show` |
| **UseCase** | `GetSpotDetailUseCase` |

#### パスパラメータ

| パラメータ | 型 | 説明 |
|-----------|-----|------|
| id | integer | スポットID |

#### レスポンス

**成功（200）**

```json
{
  "data": {
    "id": 1,
    "name": "伊勢神宮 内宮",
    "description": "日本最高峰の神社。天照大御神を祀る。",
    "address": "三重県伊勢市宇治館町1",
    "latitude": 34.4554,
    "longitude": 136.7254,
    "business_hours": "5:00-18:00（季節により変動）",
    "price_info": "参拝無料",
    "google_maps_url": "https://maps.google.com/...",
    "image_url": "https://example.com/ise-naiku.jpg",
    "category": "sightseeing",
    "sort_order": 1,
    "memos": [
      {
        "id": 1,
        "user_id": 1,
        "body": "御朱印帳を持っていく！",
        "user": {
          "id": 1,
          "name": "たろう"
        },
        "created_at": "2026-03-10T09:00:00.000000Z",
        "updated_at": "2026-03-10T09:00:00.000000Z"
      },
      {
        "id": 2,
        "user_id": 2,
        "body": "五十鈴川の写真を撮りたい",
        "user": {
          "id": 2,
          "name": "はなこ"
        },
        "created_at": "2026-03-10T10:00:00.000000Z",
        "updated_at": "2026-03-10T10:00:00.000000Z"
      }
    ],
    "photos": [
      {
        "id": 1,
        "user_id": 1,
        "storage_path": "photos/1/abc123.jpg",
        "thumbnail_path": "photos/1/abc123_thumb.jpg",
        "caption": "宇治橋からの景色",
        "taken_at": "2026-03-28T10:30:00.000000Z",
        "created_at": "2026-03-28T10:31:00.000000Z"
      }
    ],
    "created_at": "2026-03-01T00:00:00.000000Z",
    "updated_at": "2026-03-01T00:00:00.000000Z"
  }
}
```

**エラー（404）**

```json
{
  "message": "Not found."
}
```

---

### 3-3. スポットメモ作成

指定スポットにメモを追加する。

| 項目 | 内容 |
|------|------|
| **メソッド** | `POST` |
| **パス** | `/api/spots/{id}/memos` |
| **認証** | 要 |
| **コントローラー** | `SpotController@storeMemo` |

#### パスパラメータ

| パラメータ | 型 | 説明 |
|-----------|-----|------|
| id | integer | スポットID |

#### リクエストボディ

| フィールド | 型 | 必須 | バリデーション | 説明 |
|-----------|-----|------|-------------|------|
| body | string | YES | required, max:1000 | メモ本文 |

```json
{
  "body": "赤福の焼き立てを食べたい！"
}
```

#### レスポンス

**成功（201）**

```json
{
  "data": {
    "id": 3,
    "spot_id": 2,
    "user_id": 1,
    "body": "赤福の焼き立てを食べたい！",
    "user": {
      "id": 1,
      "name": "たろう"
    },
    "created_at": "2026-03-10T12:00:00.000000Z",
    "updated_at": "2026-03-10T12:00:00.000000Z"
  }
}
```

**エラー（422）**

```json
{
  "message": "The given data was invalid.",
  "errors": {
    "body": ["The body field is required."]
  }
}
```

---

### 3-4. スポットの写真一覧取得

指定スポットに紐づく写真の一覧を取得する。

| 項目 | 内容 |
|------|------|
| **メソッド** | `GET` |
| **パス** | `/api/spots/{id}/photos` |
| **認証** | 要 |
| **コントローラー** | `SpotController@photos` |

#### パスパラメータ

| パラメータ | 型 | 説明 |
|-----------|-----|------|
| id | integer | スポットID |

#### レスポンス

**成功（200）**

```json
{
  "data": [
    {
      "id": 1,
      "user_id": 1,
      "spot_id": 1,
      "storage_path": "photos/1/abc123.jpg",
      "thumbnail_path": "photos/1/abc123_thumb.jpg",
      "original_filename": "IMG_0001.jpg",
      "mime_type": "image/jpeg",
      "file_size": 2048576,
      "caption": "宇治橋からの景色",
      "taken_at": "2026-03-28T10:30:00.000000Z",
      "user": {
        "id": 1,
        "name": "たろう"
      },
      "created_at": "2026-03-28T10:31:00.000000Z",
      "updated_at": "2026-03-28T10:31:00.000000Z"
    }
  ]
}
```

---

## 4. 写真アルバム（Photo / Album）

### 4-1. 写真一覧取得

全写真の一覧を取得する。スポット別・時系列でフィルタ可能。

| 項目 | 内容 |
|------|------|
| **メソッド** | `GET` |
| **パス** | `/api/photos` |
| **認証** | 要 |
| **コントローラー** | `AlbumController@index` |
| **UseCase** | `GetAlbumUseCase` |

#### クエリパラメータ

| パラメータ | 型 | 必須 | 説明 |
|-----------|-----|------|------|
| spot_id | integer | NO | スポットIDでフィルタ |
| sort | string | NO | ソート順。`taken_at`（デフォルト）または `created_at` |
| order | string | NO | `asc` または `desc`（デフォルト: `desc`） |

#### レスポンス

**成功（200）**

```json
{
  "data": [
    {
      "id": 1,
      "user_id": 1,
      "spot_id": 1,
      "storage_path": "photos/1/abc123.jpg",
      "thumbnail_path": "photos/1/abc123_thumb.jpg",
      "original_filename": "IMG_0001.jpg",
      "mime_type": "image/jpeg",
      "file_size": 2048576,
      "caption": "宇治橋からの景色",
      "taken_at": "2026-03-28T10:30:00.000000Z",
      "user": {
        "id": 1,
        "name": "たろう"
      },
      "spot": {
        "id": 1,
        "name": "伊勢神宮 内宮"
      },
      "created_at": "2026-03-28T10:31:00.000000Z",
      "updated_at": "2026-03-28T10:31:00.000000Z"
    }
  ]
}
```

---

### 4-2. 写真アップロード

写真をアップロードする。画像ファイルは Cloudflare R2 に保存される。

| 項目 | 内容 |
|------|------|
| **メソッド** | `POST` |
| **パス** | `/api/photos` |
| **認証** | 要 |
| **Content-Type** | `multipart/form-data` |
| **コントローラー** | `AlbumController@store` |
| **UseCase** | `UploadPhotoUseCase` |

#### リクエストボディ（multipart/form-data）

| フィールド | 型 | 必須 | バリデーション | 説明 |
|-----------|-----|------|-------------|------|
| photo | file | YES | required, image, mimes:jpeg,png,gif,webp, max:10240 | 画像ファイル（最大10MB） |
| spot_id | integer | NO | nullable, exists:spots,id | 紐付けるスポットID |
| caption | string | NO | nullable, max:500 | キャプション |
| taken_at | string | NO | nullable, date | 撮影日時（日付として認識可能な文字列、ISO 8601 推奨） |

#### レスポンス

**成功（201）**

```json
{
  "data": {
    "id": 10,
    "user_id": 1,
    "spot_id": 1,
    "storage_path": "photos/1/d4e5f6g7.jpg",
    "thumbnail_path": "photos/1/d4e5f6g7_thumb.jpg",
    "original_filename": "IMG_0042.jpg",
    "mime_type": "image/jpeg",
    "file_size": 3145728,
    "caption": "おかげ横丁の入り口",
    "taken_at": "2026-03-28T13:00:00.000000Z",
    "user": {
      "id": 1,
      "name": "たろう"
    },
    "spot": {
      "id": 1,
      "name": "おかげ横丁"
    },
    "created_at": "2026-03-28T13:01:00.000000Z",
    "updated_at": "2026-03-28T13:01:00.000000Z"
  }
}
```

**エラー（422）**

```json
{
  "message": "The given data was invalid.",
  "errors": {
    "photo": ["The photo field is required."]
  }
}
```

---

### 4-3. 写真削除

指定写真を削除する。R2 上のファイルも削除される。

| 項目 | 内容 |
|------|------|
| **メソッド** | `DELETE` |
| **パス** | `/api/photos/{id}` |
| **認証** | 要 |
| **コントローラー** | `AlbumController@destroy` |

#### パスパラメータ

| パラメータ | 型 | 説明 |
|-----------|-----|------|
| id | integer | 写真ID |

#### レスポンス

| コード | 説明 |
|--------|------|
| 204 | 削除成功（レスポンスボディなし） |
| 404 | 対象写真が見つからない |

---

## 5. ふたりの掲示板（Board）

### 5-1. 掲示板投稿一覧取得

掲示板の投稿をリアクション付きで新しい順に取得する。

| 項目 | 内容 |
|------|------|
| **メソッド** | `GET` |
| **パス** | `/api/board` |
| **認証** | 要 |
| **コントローラー** | `BoardController@index` |

#### レスポンス

**成功（200）**

```json
{
  "data": [
    {
      "id": 5,
      "user_id": 2,
      "body": "赤福おいしかった！！",
      "photo_id": null,
      "is_best_shot": false,
      "user": {
        "id": 2,
        "name": "はなこ"
      },
      "photo": null,
      "reactions": [
        {
          "id": 1,
          "user_id": 1,
          "emoji": "\u2764\ufe0f",
          "user": {
            "id": 1,
            "name": "たろう"
          }
        }
      ],
      "created_at": "2026-03-28T14:00:00.000000Z",
      "updated_at": "2026-03-28T14:00:00.000000Z"
    },
    {
      "id": 4,
      "user_id": 1,
      "body": "今日のベストショット！五十鈴川がきれいだった",
      "photo_id": 3,
      "is_best_shot": true,
      "user": {
        "id": 1,
        "name": "たろう"
      },
      "photo": {
        "id": 3,
        "storage_path": "photos/1/xyz789.jpg",
        "thumbnail_path": "photos/1/xyz789_thumb.jpg",
        "caption": "五十鈴川"
      },
      "reactions": [],
      "created_at": "2026-03-28T13:30:00.000000Z",
      "updated_at": "2026-03-28T13:30:00.000000Z"
    }
  ]
}
```

---

### 5-2. 掲示板投稿作成

掲示板にメッセージを投稿する。ベストショット写真の共有にも対応。

| 項目 | 内容 |
|------|------|
| **メソッド** | `POST` |
| **パス** | `/api/board` |
| **認証** | 要 |
| **コントローラー** | `BoardController@store` |
| **UseCase** | `PostMessageUseCase` |

#### リクエストボディ

| フィールド | 型 | 必須 | バリデーション | 説明 |
|-----------|-----|------|-------------|------|
| body | string | YES | required, max:1000 | 投稿本文 |
| photo_id | integer | NO | nullable, exists:photos,id | ベストショット写真ID |
| is_best_shot | boolean | NO | nullable, boolean | ベストショットフラグ（デフォルト: false） |

```json
{
  "body": "伊勢うどん最高！",
  "photo_id": null,
  "is_best_shot": false
}
```

#### レスポンス

**成功（201）**

```json
{
  "data": {
    "id": 6,
    "user_id": 1,
    "body": "伊勢うどん最高！",
    "photo_id": null,
    "is_best_shot": false,
    "user": {
      "id": 1,
      "name": "たろう"
    },
    "photo": null,
    "reactions": [],
    "created_at": "2026-03-28T12:30:00.000000Z",
    "updated_at": "2026-03-28T12:30:00.000000Z"
  }
}
```

---

### 5-3. リアクション追加

掲示板投稿にスタンプ（リアクション）を追加する。

| 項目 | 内容 |
|------|------|
| **メソッド** | `POST` |
| **パス** | `/api/board/{id}/reactions` |
| **認証** | 要 |
| **コントローラー** | `BoardController@storeReaction` |

#### パスパラメータ

| パラメータ | 型 | 説明 |
|-----------|-----|------|
| id | integer | 掲示板投稿ID |

#### リクエストボディ

| フィールド | 型 | 必須 | バリデーション | 説明 |
|-----------|-----|------|-------------|------|
| emoji | string | YES | required, max:16 | リアクション絵文字（Unicode絵文字） |

```json
{
  "emoji": "\ud83d\ude0d"
}
```

#### レスポンス

**成功（201）**

```json
{
  "data": {
    "id": 5,
    "board_post_id": 6,
    "user_id": 2,
    "emoji": "\ud83d\ude0d",
    "user": {
      "id": 2,
      "name": "はなこ"
    },
    "created_at": "2026-03-28T12:35:00.000000Z",
    "updated_at": "2026-03-28T12:35:00.000000Z"
  }
}
```

**エラー（422）** -- 同じ絵文字で重複リアクション

```json
{
  "message": "The given data was invalid.",
  "errors": {
    "emoji": ["この絵文字は既にリアクション済みです。"]
  }
}
```

**エラー（404）** -- 投稿が存在しない

```json
{
  "message": "Not found."
}
```

---

## 6. パッキングリスト（Packing）

### 6-1. パッキング一覧取得

持ち物チェックリストの全項目を取得する。

| 項目 | 内容 |
|------|------|
| **メソッド** | `GET` |
| **パス** | `/api/packing` |
| **認証** | 要 |
| **コントローラー** | `PackingController@index` |

#### クエリパラメータ

| パラメータ | 型 | 必須 | 説明 |
|-----------|-----|------|------|
| assignee | string | NO | 担当フィルタ（self / partner / shared） |

#### レスポンス

**成功（200）**

```json
{
  "data": [
    {
      "id": 1,
      "user_id": 1,
      "name": "パスポート",
      "is_checked": false,
      "assignee": "self",
      "category": "貴重品",
      "sort_order": 0,
      "user": {
        "id": 1,
        "name": "たろう"
      },
      "created_at": "2026-03-20T10:00:00.000000Z",
      "updated_at": "2026-03-20T10:00:00.000000Z"
    },
    {
      "id": 2,
      "user_id": 1,
      "name": "充電器",
      "is_checked": true,
      "assignee": "shared",
      "category": "電子機器",
      "sort_order": 1,
      "user": {
        "id": 1,
        "name": "たろう"
      },
      "created_at": "2026-03-20T10:00:00.000000Z",
      "updated_at": "2026-03-25T08:00:00.000000Z"
    }
  ]
}
```

---

### 6-2. パッキング項目作成

持ち物リストに新しい項目を追加する。

| 項目 | 内容 |
|------|------|
| **メソッド** | `POST` |
| **パス** | `/api/packing` |
| **認証** | 要 |
| **コントローラー** | `PackingController@store` |

#### リクエストボディ

| フィールド | 型 | 必須 | バリデーション | 説明 |
|-----------|-----|------|-------------|------|
| name | string | YES | required, max:255 | 持ち物名 |
| assignee | string | NO | in:self,partner,shared | 担当（デフォルト: shared） |
| category | string | NO | nullable, max:100 | カテゴリ |
| sort_order | integer | NO | nullable, integer, min:0 | 並び順 |

```json
{
  "name": "日焼け止め",
  "assignee": "partner",
  "category": "洗面用具"
}
```

#### レスポンス

**成功（201）**

```json
{
  "data": {
    "id": 10,
    "user_id": 1,
    "name": "日焼け止め",
    "is_checked": false,
    "assignee": "partner",
    "category": "洗面用具",
    "sort_order": 0,
    "user": {
      "id": 1,
      "name": "たろう"
    },
    "created_at": "2026-03-20T12:00:00.000000Z",
    "updated_at": "2026-03-20T12:00:00.000000Z"
  }
}
```

---

### 6-3. パッキング項目更新

パッキング項目を更新する。チェック状態の切り替えにも使用する。

| 項目 | 内容 |
|------|------|
| **メソッド** | `PATCH` |
| **パス** | `/api/packing/{id}` |
| **認証** | 要 |
| **コントローラー** | `PackingController@update` |

#### パスパラメータ

| パラメータ | 型 | 説明 |
|-----------|-----|------|
| id | integer | パッキング項目ID |

#### リクエストボディ

全フィールド任意。送信したフィールドのみ更新される。

| フィールド | 型 | 必須 | バリデーション | 説明 |
|-----------|-----|------|-------------|------|
| name | string | NO | sometimes, required, max:255 | 持ち物名 |
| is_checked | boolean | NO | boolean | チェック状態 |
| assignee | string | NO | in:self,partner,shared | 担当 |
| category | string | NO | nullable, max:100 | カテゴリ |
| sort_order | integer | NO | nullable, integer, min:0 | 並び順 |

```json
{
  "is_checked": true
}
```

#### レスポンス

**成功（200）**

```json
{
  "data": {
    "id": 1,
    "user_id": 1,
    "name": "パスポート",
    "is_checked": true,
    "assignee": "self",
    "category": "貴重品",
    "sort_order": 0,
    "user": {
      "id": 1,
      "name": "たろう"
    },
    "created_at": "2026-03-20T10:00:00.000000Z",
    "updated_at": "2026-03-27T09:00:00.000000Z"
  }
}
```

---

### 6-4. パッキング項目削除

パッキング項目を削除する。

| 項目 | 内容 |
|------|------|
| **メソッド** | `DELETE` |
| **パス** | `/api/packing/{id}` |
| **認証** | 要 |
| **コントローラー** | `PackingController@destroy` |

#### パスパラメータ

| パラメータ | 型 | 説明 |
|-----------|-----|------|
| id | integer | パッキング項目ID |

#### レスポンス

| コード | 説明 |
|--------|------|
| 204 | 削除成功（レスポンスボディなし） |
| 404 | 対象リソースが見つからない |

---

## 7. 費用メモ（Expense）

### 7-1. 費用一覧取得

旅行の支出記録一覧を取得する。

| 項目 | 内容 |
|------|------|
| **メソッド** | `GET` |
| **パス** | `/api/expenses` |
| **認証** | 要 |
| **コントローラー** | `ExpenseController@index` |

#### クエリパラメータ

| パラメータ | 型 | 必須 | 説明 |
|-----------|-----|------|------|
| category | string | NO | カテゴリフィルタ（transport / food / souvenir / accommodation / other） |

#### レスポンス

**成功（200）**

```json
{
  "data": [
    {
      "id": 1,
      "user_id": 1,
      "description": "近鉄特急券（難波→伊勢市）",
      "amount": 3200,
      "category": "transport",
      "paid_at": "2026-03-28",
      "is_shared": true,
      "user": {
        "id": 1,
        "name": "たろう"
      },
      "created_at": "2026-03-28T08:00:00.000000Z",
      "updated_at": "2026-03-28T08:00:00.000000Z"
    },
    {
      "id": 2,
      "user_id": 2,
      "description": "赤福（お土産）",
      "amount": 800,
      "category": "souvenir",
      "paid_at": "2026-03-28",
      "is_shared": false,
      "user": {
        "id": 2,
        "name": "はなこ"
      },
      "created_at": "2026-03-28T14:00:00.000000Z",
      "updated_at": "2026-03-28T14:00:00.000000Z"
    }
  ]
}
```

---

### 7-2. 費用記録作成

新しい支出を記録する。

| 項目 | 内容 |
|------|------|
| **メソッド** | `POST` |
| **パス** | `/api/expenses` |
| **認証** | 要 |
| **コントローラー** | `ExpenseController@store` |
| **UseCase** | `RecordExpenseUseCase` |

#### リクエストボディ

| フィールド | 型 | 必須 | バリデーション | 説明 |
|-----------|-----|------|-------------|------|
| description | string | YES | required, max:255 | 支出内容 |
| amount | integer | YES | required, integer, min:1 | 金額（円） |
| category | string | NO | in:transport,food,souvenir,accommodation,other | カテゴリ（デフォルト: other） |
| paid_at | string | YES | required, date_format:Y-m-d | 支払日 |
| is_shared | boolean | NO | boolean | 割り勘対象（デフォルト: true） |

```json
{
  "description": "伊勢うどん（昼食）",
  "amount": 1200,
  "category": "food",
  "paid_at": "2026-03-28",
  "is_shared": true
}
```

#### レスポンス

**成功（201）**

```json
{
  "data": {
    "id": 3,
    "user_id": 1,
    "description": "伊勢うどん（昼食）",
    "amount": 1200,
    "category": "food",
    "paid_at": "2026-03-28",
    "is_shared": true,
    "user": {
      "id": 1,
      "name": "たろう"
    },
    "created_at": "2026-03-28T12:30:00.000000Z",
    "updated_at": "2026-03-28T12:30:00.000000Z"
  }
}
```

**エラー（422）**

```json
{
  "message": "The given data was invalid.",
  "errors": {
    "description": ["The description field is required."],
    "amount": ["The amount field is required."]
  }
}
```

---

### 7-3. 費用記録削除

支出記録を削除する。

| 項目 | 内容 |
|------|------|
| **メソッド** | `DELETE` |
| **パス** | `/api/expenses/{id}` |
| **認証** | 要 |
| **コントローラー** | `ExpenseController@destroy` |

#### パスパラメータ

| パラメータ | 型 | 説明 |
|-----------|-----|------|
| id | integer | 費用記録ID |

#### レスポンス

| コード | 説明 |
|--------|------|
| 204 | 削除成功（レスポンスボディなし） |
| 404 | 対象リソースが見つからない |

---

### 7-4. 費用サマリー取得

カテゴリ別合計、ユーザー別合計、割り勘精算額を算出して返す。

| 項目 | 内容 |
|------|------|
| **メソッド** | `GET` |
| **パス** | `/api/expenses/summary` |
| **認証** | 要 |
| **コントローラー** | `ExpenseController@summary` |
| **UseCase** | `GetExpenseSummaryUseCase` |

#### レスポンス

**成功（200）**

```json
{
  "data": {
    "total_amount": 25600,
    "shared_total": 20000,
    "per_person": 10000,
    "by_category": {
      "transport": 6400,
      "food": 8200,
      "souvenir": 3000,
      "accommodation": 7000,
      "other": 1000
    },
    "by_user": [
      {
        "user_id": 1,
        "name": "たろう",
        "total_paid": 15000,
        "shared_paid": 12000
      },
      {
        "user_id": 2,
        "name": "はなこ",
        "total_paid": 10600,
        "shared_paid": 8000
      }
    ],
    "settlement": {
      "from_user_id": 2,
      "from_user_name": "はなこ",
      "to_user_id": 1,
      "to_user_name": "たろう",
      "amount": 2000
    }
  }
}
```

`settlement` フィールドの説明:
- `from_user` が `to_user` に `amount` 円を支払えば精算完了
- 割り勘計算ロジック: `is_shared=1` のレコード合計を2等分し、各ユーザーの共有支出額との差分を算出

---

## 8. エクスポート（Export）

### 8-1. しおり PDF エクスポート

旅のしおりを印刷可能な PDF として出力する。

| 項目 | 内容 |
|------|------|
| **メソッド** | `POST` |
| **パス** | `/api/export/itinerary-pdf` |
| **認証** | 要 |
| **コントローラー** | `ExportController@itineraryPdf` |

#### リクエストボディ

なし（現在のしおりデータ全体を PDF 化する）

#### レスポンス

**成功（200）**

| ヘッダー | 値 |
|---------|-----|
| `Content-Type` | `application/pdf` |
| `Content-Disposition` | `attachment; filename="itinerary.pdf"` |

レスポンスボディは PDF バイナリデータ。

**エラー（500）** -- PDF 生成失敗

```json
{
  "message": "PDF の生成に失敗しました。"
}
```

---

### 8-2. フォトブック PDF エクスポート

写真とコメントをレイアウトしたフォトブック風 PDF を生成する。

| 項目 | 内容 |
|------|------|
| **メソッド** | `POST` |
| **パス** | `/api/export/photobook-pdf` |
| **認証** | 要 |
| **コントローラー** | `ExportController@photobookPdf` |

#### リクエストボディ

なし（アルバム内の全写真を PDF 化する）

#### レスポンス

**成功（200）**

| ヘッダー | 値 |
|---------|-----|
| `Content-Type` | `application/pdf` |
| `Content-Disposition` | `attachment; filename="photobook.pdf"` |

レスポンスボディは PDF バイナリデータ。

---

### 8-3. スライドショー動画エクスポート

写真を繋いだ MP4 動画を生成する。

※ サーバーサイド動画生成の複雑性から現在は 501 Not Implemented を返す。将来対応予定。

| 項目 | 内容 |
|------|------|
| **メソッド** | `POST` |
| **パス** | `/api/export/slideshow-video` |
| **認証** | 要 |
| **コントローラー** | `ExportController@slideshowVideo` |

#### リクエストボディ

なし（アルバム内の全写真を動画化する）

#### レスポンス

**成功（200）**

| ヘッダー | 値 |
|---------|-----|
| `Content-Type` | `video/mp4` |
| `Content-Disposition` | `attachment; filename="slideshow.mp4"` |

レスポンスボディは MP4 バイナリデータ。

---

### 8-4. ZIP 一括ダウンロード

全写真とメモデータをまとめた ZIP ファイルをダウンロードする。

| 項目 | 内容 |
|------|------|
| **メソッド** | `POST` |
| **パス** | `/api/export/zip` |
| **認証** | 要 |
| **コントローラー** | `ExportController@zip` |

#### リクエストボディ

なし

#### レスポンス

**成功（200）**

| ヘッダー | 値 |
|---------|-----|
| `Content-Type` | `application/zip` |
| `Content-Disposition` | `attachment; filename="ise-trip-export.zip"` |

レスポンスボディは ZIP バイナリデータ。

---

## API エンドポイント一覧（サマリー）

| # | メソッド | パス | 認証 | 概要 |
|---|---------|------|------|------|
| **認証** | | | | |
| 1 | `GET` | `/sanctum/csrf-cookie` | - | CSRF Cookie 取得 |
| 2 | `POST` | `/api/login` | - | ログイン |
| 3 | `POST` | `/api/logout` | 要 | ログアウト |
| 4 | `GET` | `/api/user` | 要 | 認証ユーザー取得 |
| **しおり** | | | | |
| 5 | `GET` | `/api/itinerary` | 要 | しおり一覧取得 |
| 6 | `POST` | `/api/itinerary` | 要 | しおり項目作成 |
| 7 | `PATCH` | `/api/itinerary/{id}` | 要 | しおり項目更新 |
| 8 | `DELETE` | `/api/itinerary/{id}` | 要 | しおり項目削除 |
| 9 | `PATCH` | `/api/itinerary/reorder` | 要 | しおり並び替え |
| **スポット** | | | | |
| 10 | `GET` | `/api/spots` | 要 | スポット一覧取得 |
| 11 | `GET` | `/api/spots/{id}` | 要 | スポット詳細取得 |
| 12 | `POST` | `/api/spots/{id}/memos` | 要 | スポットメモ作成 |
| 13 | `GET` | `/api/spots/{id}/photos` | 要 | スポット写真一覧取得 |
| **写真アルバム** | | | | |
| 14 | `GET` | `/api/photos` | 要 | 写真一覧取得 |
| 15 | `POST` | `/api/photos` | 要 | 写真アップロード |
| 16 | `DELETE` | `/api/photos/{id}` | 要 | 写真削除 |
| **掲示板** | | | | |
| 17 | `GET` | `/api/board` | 要 | 掲示板投稿一覧取得 |
| 18 | `POST` | `/api/board` | 要 | 掲示板投稿作成 |
| 19 | `POST` | `/api/board/{id}/reactions` | 要 | リアクション追加 |
| **パッキング** | | | | |
| 20 | `GET` | `/api/packing` | 要 | パッキング一覧取得 |
| 21 | `POST` | `/api/packing` | 要 | パッキング項目作成 |
| 22 | `PATCH` | `/api/packing/{id}` | 要 | パッキング項目更新 |
| 23 | `DELETE` | `/api/packing/{id}` | 要 | パッキング項目削除 |
| **費用** | | | | |
| 24 | `GET` | `/api/expenses` | 要 | 費用一覧取得 |
| 25 | `POST` | `/api/expenses` | 要 | 費用記録作成 |
| 26 | `DELETE` | `/api/expenses/{id}` | 要 | 費用記録削除 |
| 27 | `GET` | `/api/expenses/summary` | 要 | 費用サマリー取得 |
| **エクスポート** | | | | |
| 28 | `POST` | `/api/export/itinerary-pdf` | 要 | しおり PDF 出力 |
| 29 | `POST` | `/api/export/photobook-pdf` | 要 | フォトブック PDF 出力 |
| 30 | `POST` | `/api/export/slideshow-video` | 要 | スライドショー動画出力 |
| 31 | `POST` | `/api/export/zip` | 要 | ZIP 一括ダウンロード |

---

## 関連ドキュメント

- [CONTRIBUTING.md](../../CONTRIBUTING.md) -- 技術ガイド（アーキテクチャ、ディレクトリ構成）
- [ルート定義書](../route/index.md) -- フロントエンドルート定義（呼び出し API 記載）
- [DB 設計: ER図](../db/ER.md) -- テーブルリレーション
- [DB 設計: テーブル定義書](../db/table/index.md) -- 各テーブルのカラム詳細
- [企画書](../project/ise_trip_app_plan.md) -- 機能要件

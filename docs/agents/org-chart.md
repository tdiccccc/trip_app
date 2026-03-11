# エージェント組織図

最終更新: 2026-03-11

## 組織構成

```mermaid
graph TD
    PM["🔴 project-manager<br/>プロジェクトマネージャー<br/>━━━━━━━━━━━━━━━<br/>進捗管理・タスク分解<br/>作業指示・リリース計画"]

    PM --> SP
    PM --> IMPL_LEAD
    PM --> QA
    PM --> TW

    SP["🔵 service-planner<br/>サービス企画<br/>━━━━━━━━━━━━━━━<br/>機能企画・UX設計<br/>優先度判断・仕様策定"]

    subgraph IMPL["実装チーム"]
        IMPL_LEAD["🟣 backend<br/>バックエンド<br/>━━━━━━━━━━━━━━━<br/>Laravel 12 API 実装<br/>オニオンアーキテクチャ"]
        FE["🟢 frontend<br/>フロントエンド<br/>━━━━━━━━━━━━━━━<br/>Nuxt 4 / Vue 3<br/>Tailwind CSS"]
        DB["🟠 db-architect<br/>DB設計<br/>━━━━━━━━━━━━━━━<br/>テーブル設計<br/>マイグレーション"]
        DK["⚫ docker<br/>インフラ<br/>━━━━━━━━━━━━━━━<br/>Docker / Cloud Run<br/>CI/CD"]
    end

    PM --> FE
    PM --> DB
    PM --> DK

    QA["🟩 qa-tester<br/>QA / テスト<br/>━━━━━━━━━━━━━━━<br/>テスト戦略・実装<br/>品質保証"]

    TW["🟡 tech-writer<br/>テクニカルライター<br/>━━━━━━━━━━━━━━━<br/>仕様書・定義書<br/>ドキュメント整合性"]

    %% 点線: 企画→実装への仕様インプット
    SP -.->|仕様書| IMPL_LEAD
    SP -.->|仕様書| FE
    SP -.->|データ要件| DB

    %% 点線: QA→実装へのフィードバック
    QA -.->|テスト結果| IMPL_LEAD
    QA -.->|テスト結果| FE

    %% 点線: TW→各チームへのドキュメント同期
    TW -.->|整合性チェック| IMPL_LEAD
    TW -.->|整合性チェック| FE
    TW -.->|整合性チェック| DB

    classDef pm fill:#ff6b6b,stroke:#c92a2a,color:#fff,font-weight:bold
    classDef planner fill:#66d9e8,stroke:#1098ad,color:#000,font-weight:bold
    classDef impl fill:#b197fc,stroke:#7048e8,color:#fff,font-weight:bold
    classDef fe fill:#69db7c,stroke:#2b8a3e,color:#000,font-weight:bold
    classDef db fill:#ffa94d,stroke:#e8590c,color:#000,font-weight:bold
    classDef dk fill:#868e96,stroke:#495057,color:#fff,font-weight:bold
    classDef qa fill:#51cf66,stroke:#2b8a3e,color:#000,font-weight:bold
    classDef tw fill:#ffd43b,stroke:#f08c00,color:#000,font-weight:bold

    class PM pm
    class SP planner
    class IMPL_LEAD impl
    class FE fe
    class DB db
    class DK dk
    class QA qa
    class TW tw
```

## 指揮系統（実線）

| 上位 | 下位 | 関係 |
|------|------|------|
| **project-manager** | service-planner | 企画の依頼・優先度の最終決定 |
| **project-manager** | backend | API 実装タスクの指示 |
| **project-manager** | frontend | UI 実装タスクの指示 |
| **project-manager** | db-architect | DB 設計・マイグレーションの指示 |
| **project-manager** | docker | インフラ・CI/CD の指示 |
| **project-manager** | qa-tester | テスト計画・実行の指示 |
| **project-manager** | tech-writer | ドキュメント更新の指示 |

## 連携関係（点線）

| 発信元 | 受信先 | 連携内容 |
|--------|--------|---------|
| service-planner | backend, frontend | 機能仕様書のインプット |
| service-planner | db-architect | データ要件の伝達 |
| qa-tester | backend, frontend | テスト結果・不具合報告のフィードバック |
| tech-writer | backend, frontend, db-architect | ドキュメントと実装の差分報告 |

## 作業フロー

```
① 企画フェーズ
   service-planner → 機能仕様書を作成

② 計画フェーズ
   project-manager → タスク分解・優先度決定・担当割り当て

③ 設計フェーズ
   db-architect → テーブル設計・マイグレーション作成

④ 実装フェーズ
   backend  → Domain → Application → Infrastructure → Presentation
   frontend → 型定義 → composable → ページ/コンポーネント
   docker   → 必要に応じてインフラ更新

⑤ 検証フェーズ
   qa-tester → テスト設計・実行・不具合報告

⑥ 文書化フェーズ
   tech-writer → API仕様書・DB定義書・機能仕様の更新

⑦ リリース判定
   project-manager → 品質チェック・リリース承認
```

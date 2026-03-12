---
description: 進捗レポートを生成し docs/progress-management/ に保存する
allowed-tools: Agent, Bash(git log:*), Bash(git diff:*), Bash(git status:*), Glob, Grep, Read, Write
---

@project-manager に以下のタスクを依頼してください。

## タスク

`docs/progress-management/README.md` に記載のルールに従い、`docs/progress-management/` 配下に現在の進捗レポートを作成する。

## 手順

1. `docs/progress-management/README.md` を読み、命名規則・レポート構成ルールを確認する
2. `docs/progress-management/` 内の既存ファイルを確認し、本日の日付で次の連番ファイル名を決定する（今日: $CURRENT_DATE）
3. プロジェクト全体の現状を調査する:
   - git log で直近の変更を確認
   - docs/progress-management/ の最新レポートを読んで前回からの差分を把握
   - backend/ と frontend/ の実装状況を確認
   - docs/ 配下の仕様書を確認
4. README.md の「レポート構成（推奨）」に従い進捗レポートを作成・保存する
5. README.md のディレクトリ構成セクションに新しいファイルのエントリを追記する

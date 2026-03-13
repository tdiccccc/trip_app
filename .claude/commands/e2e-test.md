---
description: Playwright MCPサーバーを起動してシナリオテストを実行する
allowed-tools: mcp__playwright__browser_navigate, mcp__playwright__browser_click, mcp__playwright__browser_snapshot, mcp__playwright__browser_take_screenshot, mcp__playwright__browser_fill_form, mcp__playwright__browser_type, mcp__playwright__browser_press_key, mcp__playwright__browser_hover, mcp__playwright__browser_select_option, mcp__playwright__browser_handle_dialog, mcp__playwright__browser_wait_for, mcp__playwright__browser_tabs, mcp__playwright__browser_close, mcp__playwright__browser_console_messages, mcp__playwright__browser_network_requests, mcp__playwright__browser_resize, mcp__playwright__browser_drag, mcp__playwright__browser_evaluate, mcp__playwright__browser_file_upload, mcp__playwright__browser_install, mcp__playwright__browser_navigate_back, mcp__playwright__browser_run_code, Read, Bash(echo:*)
argument-hint: <シナリオファイルパス> (例: docs/tests/scenario/001_ログイン.md)
---

## Playwright MCP E2E テスト実行

### 実行手順

1. **シナリオファイルの読み込み**
   - 引数 `$ARGUMENTS` で指定されたシナリオファイルを Read ツールで読み込む
   - ファイルが存在しない場合はエラーメッセージを表示して終了

2. **前提条件の確認**
   - シナリオファイルの「前提条件」セクションを確認
   - Playwright MCP ブラウザでフロントエンド (http://localhost:3900) にアクセスして起動確認

3. **テストケースの順次実行**
   - シナリオファイルに記載された各テストケースを上から順番に実行
   - 各ステップで指定された Playwright MCP ツールを呼び出す
   - 各ステップ実行後、「期待結果」と実際の結果を比較
   - browser_snapshot を使って画面状態を確認する

4. **結果の報告**
   - 各テストケースの結果を ✅ PASS / ❌ FAIL で報告
   - 失敗したケースは原因と画面の状態を記載
   - 最終的なサマリーを表示:

```
## テスト結果サマリー
- シナリオ: [シナリオ名]
- 合計: X件
- ✅ PASS: X件
- ❌ FAIL: X件
- 実行日時: YYYY-MM-DD HH:MM
```

### 注意事項

- CLAUDE.md の「Playwright MCP使用ルール」を厳守すること
- コード実行（Python, JavaScript, Bash等）でのブラウザ操作は禁止
- MCP ツールの直接呼び出しのみ使用すること
- エラー発生時は回避策を探さず、エラーメッセージをそのまま報告すること
- テストケース間で認証状態のクリアが必要な場合は browser_navigate で Cookie クリア相当の操作を行うこと

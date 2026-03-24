import type { H3Event } from 'h3'

/**
 * バックエンドAPIへのプロキシユーティリティ
 *
 * runtimeConfig.apiBaseUrl を使ってランタイムでバックエンドURLを解決する。
 * routeRules の proxy はビルド時に値が確定するため、
 * Cloud Run のようにランタイムで環境変数を注入する環境では動作しない。
 */
export function proxyToBackend(event: H3Event, path: string) {
  const config = useRuntimeConfig()
  const targetUrl = `${config.apiBaseUrl}${path}`

  return proxyRequest(event, targetUrl)
}

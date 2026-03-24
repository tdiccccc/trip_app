import { proxyToBackend } from '../../utils/proxy'

/**
 * /sanctum/** へのリクエストをバックエンドにプロキシする
 *
 * Laravel Sanctum の CSRF Cookie 取得エンドポイント等を転送する。
 * runtimeConfig.apiBaseUrl をランタイムで参照する。
 */
export default defineEventHandler((event) => {
  const path = event.path
  return proxyToBackend(event, path)
})

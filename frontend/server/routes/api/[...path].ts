import { proxyToBackend } from '../../utils/proxy'

/**
 * /api/** へのリクエストをバックエンドにプロキシする
 *
 * runtimeConfig.apiBaseUrl をランタイムで参照するため、
 * 環境変数 NUXT_API_BASE_URL が本番でも正しく反映される。
 */
export default defineEventHandler((event) => {
  const path = event.path
  return proxyToBackend(event, path)
})

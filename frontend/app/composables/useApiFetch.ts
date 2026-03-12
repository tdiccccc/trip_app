import type { UseFetchOptions } from 'nuxt/app'

export const useApiFetch = <T>(
  path: MaybeRefOrGetter<string>,
  options: UseFetchOptions<T> = {},
) => {
  const config = useRuntimeConfig()
  const xsrfToken = useCookie('XSRF-TOKEN')

  // SSR時のクッキーヘッダーをトップレベルで取得
  const requestHeaders = import.meta.server ? useRequestHeaders(['cookie']) : {}

  // CSR時はdocument.cookieから最新のXSRF-TOKENを直接読み取る
  const getXsrfToken = (): string | null => {
    if (import.meta.server) {
      return xsrfToken.value ?? null
    }
    const match = document.cookie.match(/XSRF-TOKEN=([^;]+)/)
    return match ? decodeURIComponent(match[1]) : null
  }

  return useFetch<T>(path, {
    baseURL: config.public.apiBase as string,
    credentials: 'include',
    // 認証が必要なAPIはSSRで取得しても認証クッキーを転送できないため、
    // クライアントサイドでのみ実行する
    server: false,
    onRequest({ options: reqOptions }) {
      const h = (reqOptions.headers || {}) as Record<string, string>
      const token = getXsrfToken()
      if (token) {
        h['X-XSRF-TOKEN'] = token
      }
      if (import.meta.server && requestHeaders.cookie) {
        h['Cookie'] = requestHeaders.cookie
      }
      reqOptions.headers = h
    },
    ...options,
  })
}

import type { UseFetchOptions } from 'nuxt/app'

export const useApiFetch = <T>(
  path: MaybeRefOrGetter<string>,
  options: UseFetchOptions<T> = {},
) => {
  const xsrfToken = useCookie('XSRF-TOKEN')

  // SSR時のクッキーヘッダーをトップレベルで取得
  const requestHeaders = import.meta.server ? useRequestHeaders(['cookie']) : {}

  // CSR時はdocument.cookieから最新のXSRF-TOKENを直接読み取る
  const getXsrfToken = (): string | null => {
    if (import.meta.server) {
      return xsrfToken.value ?? null
    }
    const match = document.cookie.match(/XSRF-TOKEN=([^;]+)/)
    return match?.[1] ? decodeURIComponent(match[1]) : null
  }

  return useFetch(path, {
    // baseURL不要: 同一オリジンのNitro proxyを経由してバックエンドへ転送
    credentials: 'include',
    // 認証が必要なAPIはSSRで取得しても認証クッキーを転送できないため、
    // クライアントサイドでのみ実行する
    server: false,
    onRequest({ options: reqOptions }) {
      const headers = new Headers(reqOptions.headers as HeadersInit ?? {})
      const token = getXsrfToken()
      if (token) {
        headers.set('X-XSRF-TOKEN', token)
      }
      if (import.meta.server && requestHeaders.cookie) {
        headers.set('Cookie', requestHeaders.cookie)
      }
      reqOptions.headers = headers
    },
    ...options,
  } as UseFetchOptions<T>)
}

import type { FetchOptions } from 'ofetch'

export const useApiClient = () => {
  const config = useRuntimeConfig()
  const baseURL = config.public.apiBase as string

  // SSR時のクッキーヘッダーをトップレベルで取得（Nuxtコンテキスト内で呼ぶ）
  const requestHeaders = import.meta.server ? useRequestHeaders(['cookie']) : {}
  const xsrfToken = useCookie('XSRF-TOKEN')

  // CSR時はdocument.cookieから最新のXSRF-TOKENを直接読み取る
  // useCookieのrefは外部からのクッキー変更（Set-Cookie）を自動同期しないため
  const getXsrfToken = (): string | null => {
    if (import.meta.server) {
      return xsrfToken.value ?? null
    }
    const match = document.cookie.match(/XSRF-TOKEN=([^;]+)/)
    return match ? decodeURIComponent(match[1]) : null
  }

  const apiFetch = <T>(path: string, options: FetchOptions = {}) => {
    const headers: Record<string, string> = {}

    const token = getXsrfToken()
    if (token) {
      headers['X-XSRF-TOKEN'] = token
    }

    // SSR時はブラウザのクッキーをリクエストヘッダーに転送
    if (import.meta.server && requestHeaders.cookie) {
      headers['Cookie'] = requestHeaders.cookie
    }

    return $fetch<T>(path, {
      baseURL,
      credentials: 'include',
      headers,
      ...options,
    })
  }

  return { apiFetch }
}

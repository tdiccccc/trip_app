import type { FetchOptions } from 'ofetch'

export const useApiClient = () => {
  const config = useRuntimeConfig()
  const baseURL = config.public.apiBase as string

  // SSR時のクッキーヘッダーをトップレベルで取得（Nuxtコンテキスト内で呼ぶ）
  const requestHeaders = import.meta.server ? useRequestHeaders(['cookie']) : {}
  const xsrfToken = useCookie('XSRF-TOKEN')

  const apiFetch = <T>(path: string, options: FetchOptions = {}) => {
    const headers: Record<string, string> = {}

    if (xsrfToken.value) {
      headers['X-XSRF-TOKEN'] = xsrfToken.value
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

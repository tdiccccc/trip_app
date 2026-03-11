import type { UseFetchOptions } from 'nuxt/app'

export const useApiFetch = <T>(
  path: MaybeRefOrGetter<string>,
  options: UseFetchOptions<T> = {},
) => {
  const config = useRuntimeConfig()
  const xsrfToken = useCookie('XSRF-TOKEN')

  // SSR時のクッキーヘッダーをトップレベルで取得
  const requestHeaders = import.meta.server ? useRequestHeaders(['cookie']) : {}

  return useFetch<T>(path, {
    baseURL: config.public.apiBase as string,
    credentials: 'include',
    onRequest({ options: reqOptions }) {
      const h = (reqOptions.headers || {}) as Record<string, string>
      if (xsrfToken.value) {
        h['X-XSRF-TOKEN'] = xsrfToken.value
      }
      if (import.meta.server && requestHeaders.cookie) {
        h['Cookie'] = requestHeaders.cookie
      }
      reqOptions.headers = h
    },
    ...options,
  })
}

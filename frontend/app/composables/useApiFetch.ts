import type { UseFetchOptions } from 'nuxt/app'

export const useApiFetch = <T>(
  path: string,
  options: UseFetchOptions<T> = {},
) => {
  const config = useRuntimeConfig()
  const xsrfToken = useCookie('XSRF-TOKEN')

  // SSR時のクッキーヘッダーをトップレベルで取得
  const requestHeaders = import.meta.server ? useRequestHeaders(['cookie']) : {}

  const headers = computed(() => {
    const h: Record<string, string> = {}
    if (xsrfToken.value) {
      h['X-XSRF-TOKEN'] = xsrfToken.value
    }
    if (import.meta.server && requestHeaders.cookie) {
      h['Cookie'] = requestHeaders.cookie
    }
    return h
  })

  return useFetch<T>(path, {
    baseURL: config.public.apiBase as string,
    credentials: 'include',
    headers,
    ...options,
  })
}

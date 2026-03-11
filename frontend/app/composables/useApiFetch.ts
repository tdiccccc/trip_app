import type { UseFetchOptions } from 'nuxt/app'

export const useApiFetch = <T>(
  path: string,
  options: UseFetchOptions<T> = {},
) => {
  const config = useRuntimeConfig()
  const xsrfToken = useCookie('XSRF-TOKEN')

  return useFetch<T>(path, {
    baseURL: config.public.apiBase as string,
    credentials: 'include',
    headers: {
      ...(xsrfToken.value ? { 'X-XSRF-TOKEN': xsrfToken.value } : {}),
    },
    ...options,
  })
}

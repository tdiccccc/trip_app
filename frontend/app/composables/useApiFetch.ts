import type { UseFetchOptions } from 'nuxt/app'

export const useApiFetch = <T>(
  path: string,
  options: UseFetchOptions<T> = {},
) => {
  const config = useRuntimeConfig()

  return useFetch<T>(path, {
    baseURL: config.public.apiBase as string,
    credentials: 'include',
    ...options,
  })
}

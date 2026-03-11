import type { FetchOptions } from 'ofetch'

export const useApiClient = () => {
  const config = useRuntimeConfig()
  const baseURL = config.public.apiBase as string

  const apiFetch = <T>(path: string, options: FetchOptions = {}) => {
    const xsrfToken = useCookie('XSRF-TOKEN').value

    return $fetch<T>(path, {
      baseURL,
      credentials: 'include',
      headers: {
        ...(xsrfToken ? { 'X-XSRF-TOKEN': xsrfToken } : {}),
      },
      ...options,
    })
  }

  return { apiFetch }
}

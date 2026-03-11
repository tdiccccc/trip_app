import type { User, ApiResponse } from '~/types/auth'

export const useAuth = () => {
  const user = useState<User | null>('auth-user', () => null)
  const isAuthenticated = computed(() => user.value !== null)

  const login = async (email: string, password: string) => {
    const config = useRuntimeConfig()
    const baseURL = config.public.apiBase as string

    // 1. CSRF Cookie 取得
    await $fetch('/sanctum/csrf-cookie', {
      baseURL,
      credentials: 'include',
    })

    // 2. XSRF-TOKEN をクッキーから取得
    const xsrfToken = useCookie('XSRF-TOKEN').value

    // 3. ログイン
    const response = await $fetch<ApiResponse<User>>('/api/login', {
      baseURL,
      method: 'POST',
      body: { email, password },
      credentials: 'include',
      headers: {
        ...(xsrfToken ? { 'X-XSRF-TOKEN': xsrfToken } : {}),
      },
    })

    user.value = response.data
  }

  const logout = async () => {
    const config = useRuntimeConfig()
    const baseURL = config.public.apiBase as string

    try {
      const xsrfToken = useCookie('XSRF-TOKEN').value
      await $fetch('/api/logout', {
        baseURL,
        method: 'POST',
        credentials: 'include',
        headers: {
          ...(xsrfToken ? { 'X-XSRF-TOKEN': xsrfToken } : {}),
        },
      })
    } catch {
      // ログアウトAPIが失敗してもクライアント側の状態はリセットする
    }

    user.value = null
    await navigateTo('/login')
  }

  const fetchUser = async () => {
    const config = useRuntimeConfig()
    const baseURL = config.public.apiBase as string

    try {
      const response = await $fetch<ApiResponse<User>>('/api/user', {
        baseURL,
        credentials: 'include',
      })
      user.value = response.data
    } catch {
      user.value = null
    }
  }

  return {
    user,
    isAuthenticated,
    login,
    logout,
    fetchUser,
  }
}

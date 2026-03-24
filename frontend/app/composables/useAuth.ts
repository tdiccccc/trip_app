import type { User, ApiResponse } from '~/types/auth'

export const useAuth = () => {
  const user = useState<User | null>('auth-user', () => null)
  const isAuthenticated = computed(() => user.value !== null)
  const { apiFetch } = useApiClient()

  const login = async (email: string, password: string) => {
    // 1. CSRF Cookie 取得（同一オリジンのNitro proxyを経由）
    await $fetch('/sanctum/csrf-cookie', {
      credentials: 'include',
    })

    // 2. ログイン（XSRF-TOKENはapiFetchが自動付与）
    const response = await apiFetch<ApiResponse<User>>('/api/login', {
      method: 'POST',
      body: { email, password },
    })

    user.value = response.data
  }

  const logout = async () => {
    try {
      await apiFetch('/api/logout', {
        method: 'POST',
      })
    } catch {
      // ログアウトAPIが失敗してもクライアント側の状態はリセットする
    }

    user.value = null
    await navigateTo('/login')
  }

  const fetchUser = async () => {
    try {
      // CSR時: リロード時にXSRF-TOKENが失われるため、再取得する
      // SSR時: ブラウザのCookieをそのまま転送するため不要
      if (import.meta.client) {
        await $fetch('/sanctum/csrf-cookie', {
          credentials: 'include',
        })
      }

      const response = await apiFetch<ApiResponse<User>>('/api/user')
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

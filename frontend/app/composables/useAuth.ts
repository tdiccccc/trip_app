export const useAuth = () => {
  const user = useState<unknown | null>('auth-user', () => null)
  const isAuthenticated = computed(() => user.value !== null)

  const login = async (_email: string, _password: string) => {
    // TODO: Sanctum SPA 認証を実装
    // 1. GET /sanctum/csrf-cookie
    // 2. POST /api/login
  }

  const logout = async () => {
    // TODO: POST /api/logout
    user.value = null
    await navigateTo('/login')
  }

  const fetchUser = async () => {
    // TODO: GET /api/user
  }

  return {
    user,
    isAuthenticated,
    login,
    logout,
    fetchUser,
  }
}

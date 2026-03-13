export default defineNuxtRouteMiddleware(async () => {
  const { isAuthenticated, fetchUser } = useAuth()

  // 未認証の場合、ユーザー情報の取得を試みる
  if (!isAuthenticated.value) {
    await fetchUser()
  }

  // それでも未認証ならログインページへ
  if (!isAuthenticated.value) {
    return navigateTo('/login')
  }
})

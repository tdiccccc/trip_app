<script setup lang="ts">
definePageMeta({
  layout: 'blank',
})

useHead({
  title: 'ログイン - Ise Trip',
})

const { login, isAuthenticated } = useAuth()
const email = ref('')
const password = ref('')
const errorMessage = ref('')
const isLoading = ref(false)

// Already authenticated -> redirect
if (isAuthenticated.value) {
  navigateTo('/trips')
}

const handleLogin = async () => {
  errorMessage.value = ''
  isLoading.value = true
  try {
    await login(email.value, password.value)
    await navigateTo('/')
  } catch (error: unknown) {
    if (
      error !== null
      && typeof error === 'object'
      && 'data' in error
      && error.data !== null
      && typeof error.data === 'object'
      && 'message' in (error.data as Record<string, unknown>)
    ) {
      errorMessage.value = (error.data as { message: string }).message
    } else {
      errorMessage.value = 'メールアドレスまたはパスワードが正しくありません。'
    }
  } finally {
    isLoading.value = false
  }
}
</script>

<template>
  <div class="flex min-h-dvh items-center justify-center px-4">
    <div class="w-full max-w-sm">
      <h1 class="mb-2 text-center text-2xl font-bold text-primary-700">
        Ise Trip
      </h1>
      <p class="mb-8 text-center text-sm text-gray-400">
        ログインして続けましょう
      </p>

      <form
        class="space-y-5"
        @submit.prevent="handleLogin"
      >
        <div
          v-if="errorMessage"
          class="rounded-xl bg-red-50 p-3 text-sm text-red-600"
          role="alert"
        >
          {{ errorMessage }}
        </div>

        <div>
          <label
            for="email"
            class="mb-1 block text-sm font-medium text-gray-700"
          >
            メールアドレス
          </label>
          <input
            id="email"
            v-model="email"
            type="email"
            required
            autocomplete="email"
            placeholder="mail@example.com"
            class="w-full rounded-xl border border-gray-300 px-4 py-3 text-base outline-none transition focus:border-primary-400 focus:ring-2 focus:ring-primary-200"
          >
        </div>

        <div>
          <label
            for="password"
            class="mb-1 block text-sm font-medium text-gray-700"
          >
            パスワード
          </label>
          <input
            id="password"
            v-model="password"
            type="password"
            required
            autocomplete="current-password"
            placeholder="パスワード"
            class="w-full rounded-xl border border-gray-300 px-4 py-3 text-base outline-none transition focus:border-primary-400 focus:ring-2 focus:ring-primary-200"
          >
        </div>

        <button
          type="submit"
          :disabled="isLoading"
          class="w-full rounded-xl bg-primary-500 py-3 text-base font-semibold text-white transition hover:bg-primary-600 active:bg-primary-700 disabled:cursor-not-allowed disabled:opacity-50"
        >
          <span v-if="isLoading">ログイン中...</span>
          <span v-else>ログイン</span>
        </button>
      </form>
    </div>
  </div>
</template>

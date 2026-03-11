<script setup lang="ts">
definePageMeta({
  layout: 'blank',
})

useHead({
  title: '\u30ED\u30B0\u30A4\u30F3 - Ise Trip',
})

const { login, isAuthenticated } = useAuth()
const email = ref('')
const password = ref('')
const errorMessage = ref('')
const isLoading = ref(false)

// Already authenticated -> redirect
if (isAuthenticated.value) {
  navigateTo('/')
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
      errorMessage.value = '\u30E1\u30FC\u30EB\u30A2\u30C9\u30EC\u30B9\u307E\u305F\u306F\u30D1\u30B9\u30EF\u30FC\u30C9\u304C\u6B63\u3057\u304F\u3042\u308A\u307E\u305B\u3093\u3002'
    }
  } finally {
    isLoading.value = false
  }
}
</script>

<template>
  <div class="flex min-h-dvh items-center justify-center px-4">
    <div class="w-full max-w-sm">
      <h1 class="mb-2 text-center text-2xl font-bold text-amber-700">
        Ise Trip
      </h1>
      <p class="mb-8 text-center text-sm text-gray-400">
        Sign in to continue
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
            Email
          </label>
          <input
            id="email"
            v-model="email"
            type="email"
            required
            autocomplete="email"
            placeholder="mail@example.com"
            class="w-full rounded-xl border border-gray-300 px-4 py-3 text-base outline-none transition focus:border-amber-400 focus:ring-2 focus:ring-amber-200"
          >
        </div>

        <div>
          <label
            for="password"
            class="mb-1 block text-sm font-medium text-gray-700"
          >
            Password
          </label>
          <input
            id="password"
            v-model="password"
            type="password"
            required
            autocomplete="current-password"
            placeholder="\u30D1\u30B9\u30EF\u30FC\u30C9"
            class="w-full rounded-xl border border-gray-300 px-4 py-3 text-base outline-none transition focus:border-amber-400 focus:ring-2 focus:ring-amber-200"
          >
        </div>

        <button
          type="submit"
          :disabled="isLoading"
          class="w-full rounded-xl bg-amber-500 py-3 text-base font-semibold text-white transition hover:bg-amber-600 active:bg-amber-700 disabled:cursor-not-allowed disabled:opacity-50"
        >
          <span v-if="isLoading">\u30ED\u30B0\u30A4\u30F3\u4E2D...</span>
          <span v-else>\u30ED\u30B0\u30A4\u30F3</span>
        </button>
      </form>
    </div>
  </div>
</template>

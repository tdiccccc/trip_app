<script setup lang="ts">
import type { User } from '~/types/auth'

definePageMeta({
  middleware: ['auth'],
})

useHead({
  title: '旅行を作成 - Trip App',
})

const { createTrip } = useTrips()
const { apiFetch } = useApiClient()

// Form data
const title = ref('')
const description = ref('')
const destination = ref('')
const startDate = ref('')
const endDate = ref('')
const selectedMemberIds = ref<number[]>([])

// Fetch available users for member selection
const users = ref<User[]>([])
const { user: currentUser } = useAuth()

onMounted(async () => {
  try {
    const response = await apiFetch<{ data: User[] }>('/api/users')
    // Exclude current user from selectable members
    users.value = response.data.filter(u => u.id !== currentUser.value?.id)
  } catch {
    // Users list is optional for alpha
  }
})

const toggleMember = (userId: number) => {
  const idx = selectedMemberIds.value.indexOf(userId)
  if (idx >= 0) {
    selectedMemberIds.value.splice(idx, 1)
  } else {
    selectedMemberIds.value.push(userId)
  }
}

// Submission
const isSubmitting = ref(false)
const errorMessage = ref('')

const handleSubmit = async () => {
  if (!title.value.trim() || !startDate.value || !endDate.value) return
  if (isSubmitting.value) return

  isSubmitting.value = true
  errorMessage.value = ''

  try {
    const response = await createTrip({
      title: title.value.trim(),
      description: description.value.trim() || undefined,
      destination: destination.value.trim() || undefined,
      start_date: startDate.value,
      end_date: endDate.value,
      member_ids: selectedMemberIds.value.length > 0 ? selectedMemberIds.value : undefined,
    })
    await navigateTo(`/trips/${response.data.id}`)
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
      errorMessage.value = '旅行の作成に失敗しました。'
    }
  } finally {
    isSubmitting.value = false
  }
}
</script>

<template>
  <div>
    <!-- Header -->
    <div class="mb-6 flex items-center gap-3">
      <NuxtLink
        to="/trips"
        class="flex h-8 w-8 items-center justify-center rounded-full text-gray-500 hover:bg-gray-100"
      >
        <svg
          class="h-5 w-5"
          fill="none"
          stroke="currentColor"
          viewBox="0 0 24 24"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M15 19l-7-7 7-7"
          />
        </svg>
      </NuxtLink>
      <h1 class="text-xl font-bold text-gray-800">
        新しい旅行を作成
      </h1>
    </div>

    <!-- Error message -->
    <div
      v-if="errorMessage"
      class="mb-4 rounded-xl bg-red-50 p-3 text-sm text-red-600"
      role="alert"
    >
      {{ errorMessage }}
    </div>

    <!-- Form -->
    <form
      class="space-y-4"
      @submit.prevent="handleSubmit"
    >
      <!-- Title -->
      <div>
        <label
          for="title"
          class="mb-1 block text-sm font-medium text-gray-700"
        >
          タイトル <span class="text-red-400">*</span>
        </label>
        <input
          id="title"
          v-model="title"
          type="text"
          required
          placeholder="例: 伊勢旅行 2026"
          class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-400 focus:outline-none focus:ring-2 focus:ring-primary-200"
        >
      </div>

      <!-- Destination -->
      <div>
        <label
          for="destination"
          class="mb-1 block text-sm font-medium text-gray-700"
        >
          行き先
        </label>
        <input
          id="destination"
          v-model="destination"
          type="text"
          placeholder="例: 三重県伊勢市"
          class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-400 focus:outline-none focus:ring-2 focus:ring-primary-200"
        >
      </div>

      <!-- Dates -->
      <div class="flex gap-3">
        <div class="flex-1">
          <label
            for="start_date"
            class="mb-1 block text-sm font-medium text-gray-700"
          >
            開始日 <span class="text-red-400">*</span>
          </label>
          <input
            id="start_date"
            v-model="startDate"
            type="date"
            required
            class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-400 focus:outline-none focus:ring-2 focus:ring-primary-200"
          >
        </div>
        <div class="flex-1">
          <label
            for="end_date"
            class="mb-1 block text-sm font-medium text-gray-700"
          >
            終了日 <span class="text-red-400">*</span>
          </label>
          <input
            id="end_date"
            v-model="endDate"
            type="date"
            required
            :min="startDate"
            class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-400 focus:outline-none focus:ring-2 focus:ring-primary-200"
          >
        </div>
      </div>

      <!-- Description -->
      <div>
        <label
          for="description"
          class="mb-1 block text-sm font-medium text-gray-700"
        >
          説明
        </label>
        <textarea
          id="description"
          v-model="description"
          rows="3"
          placeholder="旅行の説明（任意）"
          class="w-full resize-none rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-400 focus:outline-none focus:ring-2 focus:ring-primary-200"
        />
      </div>

      <!-- Member selection -->
      <div v-if="users.length > 0">
        <label class="mb-2 block text-sm font-medium text-gray-700">
          メンバーを追加
        </label>
        <div class="space-y-2">
          <button
            v-for="u in users"
            :key="u.id"
            type="button"
            class="flex w-full items-center gap-3 rounded-xl border px-4 py-3 text-left text-sm transition-colors"
            :class="selectedMemberIds.includes(u.id)
              ? 'border-primary-400 bg-primary-50'
              : 'border-gray-200 bg-white hover:bg-gray-50'"
            @click="toggleMember(u.id)"
          >
            <div
              class="flex h-6 w-6 items-center justify-center rounded-lg border-2 transition-colors"
              :class="selectedMemberIds.includes(u.id)
                ? 'border-primary-500 bg-primary-500'
                : 'border-gray-300'"
            >
              <svg
                v-if="selectedMemberIds.includes(u.id)"
                class="h-4 w-4 text-white"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2.5"
                  d="M5 13l4 4L19 7"
                />
              </svg>
            </div>
            <span class="text-gray-800">{{ u.name }}</span>
            <span class="text-xs text-gray-400">{{ u.email }}</span>
          </button>
        </div>
      </div>

      <!-- Submit -->
      <button
        type="submit"
        :disabled="!title.trim() || !startDate || !endDate || isSubmitting"
        class="w-full rounded-xl bg-primary-500 py-3 text-sm font-semibold text-white transition-colors hover:bg-primary-600 disabled:cursor-not-allowed disabled:opacity-50"
      >
        <span v-if="isSubmitting">作成中...</span>
        <span v-else>旅行を作成</span>
      </button>
    </form>
  </div>
</template>

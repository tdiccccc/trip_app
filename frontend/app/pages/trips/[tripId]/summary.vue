<script setup lang="ts">
import type { TripSummary } from '~/types/summary'

definePageMeta({
  middleware: ['auth'],
})

useHead({
  title: 'サマリー - Ise Trip',
})

const route = useRoute()
const tripId = route.params.tripId as string

const { fetchSummary } = useSummary(tripId)
const { data: response, pending } = fetchSummary()

const summary = computed<TripSummary | null>(() => {
  return response.value?.data ?? null
})

const formatAmount = (amount: number) => {
  return `\u00A5${amount.toLocaleString()}`
}

const formatDateTime = (dateStr: string | null) => {
  if (!dateStr) return '-'
  const d = new Date(dateStr)
  const month = d.getMonth() + 1
  const day = d.getDate()
  const hours = String(d.getHours()).padStart(2, '0')
  const minutes = String(d.getMinutes()).padStart(2, '0')
  return `${month}/${day} ${hours}:${minutes}`
}

const packingPercent = computed(() => {
  if (!summary.value || summary.value.packing_total === 0) return 0
  return Math.round((summary.value.packing_checked / summary.value.packing_total) * 100)
})

interface StatCard {
  label: string
  value: string
  icon: string
  color: string
}

const statCards = computed<StatCard[]>(() => {
  if (!summary.value) return []
  return [
    {
      label: '枚の写真',
      value: String(summary.value.photo_count),
      icon: 'photo',
      color: 'text-pink-500',
    },
    {
      label: 'か所',
      value: String(summary.value.spot_count),
      icon: 'spot',
      color: 'text-blue-500',
    },
    {
      label: '件の投稿',
      value: String(summary.value.board_post_count),
      icon: 'board',
      color: 'text-green-500',
    },
    {
      label: 'リアクション',
      value: String(summary.value.reaction_count),
      icon: 'reaction',
      color: 'text-red-500',
    },
    {
      label: '費用合計',
      value: formatAmount(summary.value.total_expense),
      icon: 'expense',
      color: 'text-amber-500',
    },
    {
      label: 'ひとりあたり',
      value: formatAmount(summary.value.expense_per_person),
      icon: 'per-person',
      color: 'text-purple-500',
    },
  ]
})
</script>

<template>
  <div>
    <!-- Header -->
    <div class="mb-6 flex items-center gap-3">
      <NuxtLink
        :to="`/trips/${tripId}`"
        class="flex h-8 w-8 items-center justify-center rounded-full text-gray-500 transition-colors hover:bg-gray-100"
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
        サマリー
      </h1>
    </div>

    <!-- Loading skeleton -->
    <div
      v-if="pending && !summary"
      class="space-y-4"
    >
      <div class="grid grid-cols-2 gap-3">
        <div
          v-for="i in 6"
          :key="i"
          class="h-24 animate-pulse rounded-2xl bg-gray-100"
        />
      </div>
      <div class="h-20 animate-pulse rounded-2xl bg-gray-100" />
      <div class="h-16 animate-pulse rounded-2xl bg-gray-100" />
    </div>

    <!-- Content -->
    <div
      v-else-if="summary"
      class="space-y-4"
    >
      <!-- Stat cards grid -->
      <div class="grid grid-cols-2 gap-3">
        <div
          v-for="card in statCards"
          :key="card.label"
          class="flex flex-col items-center justify-center gap-1 rounded-2xl bg-white p-4 shadow-sm"
        >
          <!-- Photo icon -->
          <svg
            v-if="card.icon === 'photo'"
            :class="['h-6 w-6', card.color]"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="1.5"
              d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"
            />
          </svg>
          <!-- Spot icon -->
          <svg
            v-if="card.icon === 'spot'"
            :class="['h-6 w-6', card.color]"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="1.5"
              d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"
            />
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="1.5"
              d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"
            />
          </svg>
          <!-- Board icon -->
          <svg
            v-if="card.icon === 'board'"
            :class="['h-6 w-6', card.color]"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="1.5"
              d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"
            />
          </svg>
          <!-- Reaction icon -->
          <svg
            v-if="card.icon === 'reaction'"
            :class="['h-6 w-6', card.color]"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="1.5"
              d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"
            />
          </svg>
          <!-- Expense icon -->
          <svg
            v-if="card.icon === 'expense'"
            :class="['h-6 w-6', card.color]"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="1.5"
              d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
            />
          </svg>
          <!-- Per-person icon -->
          <svg
            v-if="card.icon === 'per-person'"
            :class="['h-6 w-6', card.color]"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="1.5"
              d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
            />
          </svg>

          <p class="text-2xl font-bold text-gray-800">
            {{ card.value }}
          </p>
          <p class="text-xs text-gray-500">
            {{ card.label }}
          </p>
        </div>
      </div>

      <!-- Photo timeline -->
      <div class="rounded-2xl bg-white p-4 shadow-sm">
        <div class="flex items-center justify-between border-b border-gray-100 pb-3">
          <span class="text-sm text-gray-500">
            最初の写真
          </span>
          <span class="text-sm font-medium text-gray-800">
            {{ formatDateTime(summary.first_photo_at) }}
          </span>
        </div>
        <div class="flex items-center justify-between pt-3">
          <span class="text-sm text-gray-500">
            最後の写真
          </span>
          <span class="text-sm font-medium text-gray-800">
            {{ formatDateTime(summary.last_photo_at) }}
          </span>
        </div>
      </div>

      <!-- Packing progress -->
      <div class="rounded-2xl bg-white p-4 shadow-sm">
        <div class="mb-2 flex items-center justify-between">
          <span class="text-sm font-medium text-gray-800">
            パッキング
          </span>
          <span class="text-sm text-gray-500">
            {{ summary.packing_checked }}/{{ summary.packing_total }} ({{ packingPercent }}%)
          </span>
        </div>
        <div class="h-3 w-full overflow-hidden rounded-full bg-gray-100">
          <div
            class="h-full rounded-full bg-primary-500 transition-all duration-500"
            :style="{ width: `${packingPercent}%` }"
          />
        </div>
      </div>

      <!-- Trip days -->
      <div class="rounded-2xl bg-white p-4 shadow-sm">
        <div class="flex items-center justify-between">
          <span class="text-sm text-gray-500">
            旅行日数
          </span>
          <span class="text-sm font-bold text-gray-800">
            {{ summary.trip_days }}日間
          </span>
        </div>
      </div>
    </div>

    <!-- Error state -->
    <div
      v-else
      class="py-16 text-center"
    >
      <p class="text-sm text-gray-400">
        データを取得できませんでした
      </p>
    </div>
  </div>
</template>

<script setup lang="ts">
definePageMeta({
  middleware: ['auth'],
})

const route = useRoute()
const tripId = route.params.tripId as string

const { fetchTrip, deleteTrip } = useTrips()
const { data: response, refresh: refreshTrip } = fetchTrip(tripId)

// Edit modal
const showEditModal = ref(false)
const handleUpdated = async () => {
  showEditModal.value = false
  await refreshTrip()
}

const trip = computed(() => response.value?.data ?? null)

useHead({
  title: computed(() => trip.value ? `${trip.value.title} - Trip App` : 'Trip App'),
})

// Countdown timer
const now = ref(new Date())
let timer: ReturnType<typeof setInterval> | null = null

onMounted(() => {
  timer = setInterval(() => {
    now.value = new Date()
  }, 1000)
})

onUnmounted(() => {
  if (timer) clearInterval(timer)
})

const countdown = computed(() => {
  if (!trip.value) return { days: 0, hours: 0, minutes: 0, seconds: 0, isPast: true }

  const tripDate = new Date(trip.value.start_date + 'T00:00:00+09:00')
  const diff = tripDate.getTime() - now.value.getTime()

  if (diff <= 0) {
    return { days: 0, hours: 0, minutes: 0, seconds: 0, isPast: true }
  }

  const days = Math.floor(diff / (1000 * 60 * 60 * 24))
  const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60))
  const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60))
  const seconds = Math.floor((diff % (1000 * 60)) / 1000)
  return { days, hours, minutes, seconds, isPast: false }
})

const formatDateRange = (startDate: string, endDate: string) => {
  const start = new Date(startDate + 'T00:00:00')
  const end = new Date(endDate + 'T00:00:00')
  const startStr = `${start.getFullYear()}.${String(start.getMonth() + 1).padStart(2, '0')}.${String(start.getDate()).padStart(2, '0')}`
  const endStr = `${String(end.getMonth() + 1).padStart(2, '0')}.${String(end.getDate()).padStart(2, '0')}`
  return `${startStr} - ${endStr}`
}

const isOwner = computed(() => trip.value?.current_user_role === 'owner')

const quickLinks = computed(() => [
  { to: `/trips/${tripId}/itinerary`, label: 'しおり', description: '旅行の予定を確認', icon: 'calendar' },
  { to: `/trips/${tripId}/now`, label: '今どこ', description: 'GPS で現在地確認', icon: 'location' },
  { to: `/trips/${tripId}/album`, label: 'アルバム', description: '写真を見る・追加', icon: 'photo' },
  { to: `/trips/${tripId}/board`, label: '掲示板', description: 'ふたりの伝言板', icon: 'board' },
  { to: `/trips/${tripId}/packing`, label: 'パッキング', description: '持ち物チェック', icon: 'packing' },
  { to: `/trips/${tripId}/expenses`, label: '費用メモ', description: '支出を記録・集計', icon: 'expenses' },
  { to: `/trips/${tripId}/export`, label: 'エクスポート', description: '思い出を書き出し', icon: 'export' },
  { to: `/trips/${tripId}/summary`, label: 'サマリー', description: '旅行の統計情報', icon: 'summary' },
])

// Delete trip
const handleDelete = async () => {
  if (!confirm('この旅行を削除しますか？紐づく全てのデータが削除されます。')) return
  try {
    await deleteTrip(tripId)
    await navigateTo('/trips')
  } catch {
    // Error handling
  }
}
</script>

<template>
  <div
    v-if="trip"
    class="flex flex-col items-center gap-6 py-8"
  >
    <!-- Title -->
    <div class="text-center">
      <div class="flex items-center justify-center gap-2">
        <h1 class="text-3xl font-bold text-primary-700">
          {{ trip.title }}
        </h1>
        <button
          v-if="isOwner"
          class="flex h-8 w-8 items-center justify-center rounded-full text-gray-400 hover:bg-gray-100"
          @click="showEditModal = true"
        >
          <svg
            class="h-4 w-4"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"
            />
          </svg>
        </button>
      </div>
      <p class="mt-1 text-sm text-gray-400">
        {{ formatDateRange(trip.start_date, trip.end_date) }}
      </p>
      <p
        v-if="trip.destination"
        class="mt-0.5 text-sm text-gray-500"
      >
        {{ trip.destination }}
      </p>
    </div>

    <!-- Members -->
    <div class="flex items-center gap-2">
      <span
        v-for="member in trip.members"
        :key="member.id"
        class="rounded-full px-3 py-1 text-xs font-medium"
        :class="member.role === 'owner'
          ? 'bg-primary-100 text-primary-700'
          : 'bg-gray-100 text-gray-600'"
      >
        {{ member.name }}
      </span>
    </div>

    <!-- Countdown -->
    <div
      v-if="!countdown.isPast"
      class="w-full rounded-2xl bg-white p-6 shadow-sm"
    >
      <p class="mb-3 text-center text-sm text-gray-500">
        旅行まであと
      </p>
      <div class="flex justify-center gap-4">
        <div class="text-center">
          <div class="text-3xl font-bold text-primary-600">
            {{ countdown.days }}
          </div>
          <div class="text-xs text-gray-400">
            日
          </div>
        </div>
        <div class="text-center">
          <div class="text-3xl font-bold text-primary-600">
            {{ countdown.hours }}
          </div>
          <div class="text-xs text-gray-400">
            時間
          </div>
        </div>
        <div class="text-center">
          <div class="text-3xl font-bold text-primary-600">
            {{ countdown.minutes }}
          </div>
          <div class="text-xs text-gray-400">
            分
          </div>
        </div>
        <div class="text-center">
          <div class="text-3xl font-bold text-primary-600">
            {{ countdown.seconds }}
          </div>
          <div class="text-xs text-gray-400">
            秒
          </div>
        </div>
      </div>
    </div>

    <!-- Trip started message -->
    <div
      v-else
      class="w-full rounded-2xl bg-primary-500 p-6 text-center text-white shadow-sm"
    >
      <p class="text-lg font-bold">
        素敵な旅を楽しんでね！
      </p>
    </div>

    <!-- Quick links -->
    <div class="w-full grid grid-cols-2 gap-3">
      <NuxtLink
        v-for="link in quickLinks"
        :key="link.to"
        :to="link.to"
        class="flex flex-col items-center gap-2 rounded-2xl bg-white p-4 shadow-sm transition-shadow hover:shadow-md"
      >
        <!-- Calendar icon (しおり) -->
        <svg
          v-if="link.icon === 'calendar'"
          class="h-8 w-8 text-primary-500"
          fill="none"
          stroke="currentColor"
          viewBox="0 0 24 24"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="1.5"
            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
          />
        </svg>
        <!-- Location icon (今どこ) -->
        <svg
          v-if="link.icon === 'location'"
          class="h-8 w-8 text-primary-500"
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
        <!-- Photo icon (アルバム) -->
        <svg
          v-if="link.icon === 'photo'"
          class="h-8 w-8 text-primary-500"
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
        <!-- Board icon (掲示板) -->
        <svg
          v-if="link.icon === 'board'"
          class="h-8 w-8 text-primary-500"
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
        <!-- Packing icon (パッキング) -->
        <svg
          v-if="link.icon === 'packing'"
          class="h-8 w-8 text-primary-500"
          fill="none"
          stroke="currentColor"
          viewBox="0 0 24 24"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="1.5"
            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"
          />
        </svg>
        <!-- Expenses icon (費用メモ) -->
        <svg
          v-if="link.icon === 'expenses'"
          class="h-8 w-8 text-primary-500"
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
        <!-- Export icon (エクスポート) -->
        <svg
          v-if="link.icon === 'export'"
          class="h-8 w-8 text-primary-500"
          fill="none"
          stroke="currentColor"
          viewBox="0 0 24 24"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="1.5"
            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
          />
        </svg>
        <!-- Summary icon (サマリー) -->
        <svg
          v-if="link.icon === 'summary'"
          class="h-8 w-8 text-primary-500"
          fill="none"
          stroke="currentColor"
          viewBox="0 0 24 24"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="1.5"
            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"
          />
        </svg>
        <div class="text-center">
          <div class="text-sm font-semibold text-gray-800">
            {{ link.label }}
          </div>
          <div class="text-xs text-gray-400">
            {{ link.description }}
          </div>
        </div>
      </NuxtLink>
    </div>

    <!-- Owner actions -->
    <div
      v-if="isOwner"
      class="w-full pt-4"
    >
      <button
        class="w-full rounded-xl border border-red-200 py-2.5 text-sm font-medium text-red-500 transition-colors hover:bg-red-50"
        @click="handleDelete"
      >
        この旅行を削除
      </button>
    </div>

    <!-- Edit modal -->
    <TripEditModal
      v-if="showEditModal"
      :trip="trip"
      @close="showEditModal = false"
      @updated="handleUpdated"
    />
  </div>

  <!-- Loading state -->
  <div
    v-else
    class="py-16 text-center"
  >
    <p class="text-sm text-gray-400">
      読み込み中...
    </p>
  </div>
</template>

<script setup lang="ts">
definePageMeta({
  middleware: ['auth'],
})

const route = useRoute()
const tripId = route.params.tripId as string

const { fetchTrip, deleteTrip } = useTrips()
const { data: response } = fetchTrip(tripId)

const trip = computed(() => response.value?.data ?? null)

useHead({
  title: computed(() => trip.value ? `${trip.value.title} - Ise Trip` : 'Ise Trip'),
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
  { to: `/trips/${tripId}/itinerary`, label: 'しおり', description: '旅行の予定を確認' },
  { to: `/trips/${tripId}/album`, label: 'アルバム', description: '写真を見る・追加' },
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
      <h1 class="text-3xl font-bold text-primary-700">
        {{ trip.title }}
      </h1>
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
    <div class="w-full space-y-3">
      <NuxtLink
        v-for="link in quickLinks"
        :key="link.to"
        :to="link.to"
        class="flex items-center gap-4 rounded-2xl bg-white p-4 shadow-sm transition-shadow hover:shadow-md"
      >
        <div class="min-w-0 flex-1">
          <div class="text-base font-semibold text-gray-800">
            {{ link.label }}
          </div>
          <div class="text-sm text-gray-400">
            {{ link.description }}
          </div>
        </div>
        <svg
          class="h-5 w-5 shrink-0 text-gray-300"
          fill="none"
          stroke="currentColor"
          viewBox="0 0 24 24"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M9 5l7 7-7 7"
          />
        </svg>
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

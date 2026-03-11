<script setup lang="ts">
definePageMeta({
  middleware: ['auth'],
})

useHead({
  title: 'Ise Trip - トップ',
})

// Trip date: 2026-03-28
const TRIP_DATE = new Date('2026-03-28T00:00:00+09:00')

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
  const diff = TRIP_DATE.getTime() - now.value.getTime()
  if (diff <= 0) {
    return { days: 0, hours: 0, minutes: 0, seconds: 0, isPast: true }
  }
  const days = Math.floor(diff / (1000 * 60 * 60 * 24))
  const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60))
  const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60))
  const seconds = Math.floor((diff % (1000 * 60)) / 1000)
  return { days, hours, minutes, seconds, isPast: false }
})

const quickLinks = [
  { to: '/itinerary', label: 'しおり', description: '旅行の予定を確認' },
  { to: '/album', label: 'アルバム', description: '写真を見る・追加' },
]
</script>

<template>
  <div class="flex flex-col items-center gap-6 py-8">
    <!-- Title -->
    <div class="text-center">
      <h1 class="text-3xl font-bold text-primary-700">
        Ise Trip
      </h1>
      <p class="mt-1 text-sm text-gray-400">
        2026.03.28 - 03.29
      </p>
    </div>

    <!-- Countdown -->
    <div
      v-if="!countdown.isPast"
      class="w-full rounded-2xl bg-white p-6 shadow-sm"
    >
      <p class="mb-3 text-center text-sm text-gray-500">
        Until the trip
      </p>
      <div class="flex justify-center gap-4">
        <div class="text-center">
          <div class="text-3xl font-bold text-primary-600">
            {{ countdown.days }}
          </div>
          <div class="text-xs text-gray-400">
            days
          </div>
        </div>
        <div class="text-center">
          <div class="text-3xl font-bold text-primary-600">
            {{ countdown.hours }}
          </div>
          <div class="text-xs text-gray-400">
            hours
          </div>
        </div>
        <div class="text-center">
          <div class="text-3xl font-bold text-primary-600">
            {{ countdown.minutes }}
          </div>
          <div class="text-xs text-gray-400">
            min
          </div>
        </div>
        <div class="text-center">
          <div class="text-3xl font-bold text-primary-600">
            {{ countdown.seconds }}
          </div>
          <div class="text-xs text-gray-400">
            sec
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
        Have a great trip!
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
  </div>
</template>

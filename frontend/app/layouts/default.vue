<script setup lang="ts">
const { isAuthenticated, logout } = useAuth()
const route = useRoute()

// Extract tripId from route if available
const tripId = computed(() => {
  const params = route.params
  return (params.tripId as string) ?? null
})

// Fetch trip data for header title when inside a trip
const { fetchTrip } = useTrips()
const tripIdForFetch = computed(() => tripId.value ?? '')
const { data: tripResponse } = fetchTrip(tripIdForFetch)
const tripTitle = computed(() => {
  if (!tripId.value || !tripResponse.value?.data) return null
  return tripResponse.value.data.title
})

// Navigation items scoped to current trip (5 items)
const navItems = computed(() => {
  if (!tripId.value) return []
  const base = `/trips/${tripId.value}`
  return [
    { to: `${base}/itinerary`, label: 'しおり', icon: 'calendar' },
    { to: `${base}/album`, label: 'アルバム', icon: 'photo' },
    { to: `${base}/board`, label: '掲示板', icon: 'board' },
    { to: `${base}/packing`, label: 'パッキング', icon: 'packing' },
    { to: `${base}/expenses`, label: '費用', icon: 'expenses' },
  ]
})

// Header link: go to trip top if inside a trip, otherwise trips list
const headerLink = computed(() => {
  if (tripId.value) return `/trips/${tripId.value}`
  return '/trips'
})
</script>

<template>
  <div class="flex min-h-dvh flex-col bg-primary-50/30">
    <!-- Header -->
    <header class="sticky top-0 z-40 border-b border-primary-100 bg-white/90 backdrop-blur-sm">
      <div class="flex items-center justify-between px-4 py-3">
        <div class="flex items-center gap-2">
          <NuxtLink
            v-if="tripId"
            to="/trips"
            class="flex h-8 w-8 items-center justify-center rounded-full text-gray-400 hover:bg-gray-100 hover:text-gray-600"
            aria-label="旅行一覧に戻る"
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
          <NuxtLink
            :to="headerLink"
            class="text-lg font-bold text-primary-700"
          >
            {{ tripTitle ?? 'Trip App' }}
          </NuxtLink>
        </div>
        <button
          v-if="isAuthenticated"
          class="text-sm text-gray-500 hover:text-gray-700"
          @click="logout"
        >
          ログアウト
        </button>
      </div>
    </header>

    <!-- Main content -->
    <main class="flex-1 px-4 py-4">
      <slot />
    </main>

    <!-- Bottom navigation -->
    <nav
      v-if="isAuthenticated && navItems.length > 0"
      class="sticky bottom-0 z-40 border-t border-primary-100 bg-white/95 backdrop-blur-sm"
    >
      <div class="flex justify-around py-2">
        <NuxtLink
          v-for="item in navItems"
          :key="item.to"
          :to="item.to"
          class="flex flex-col items-center gap-0.5 px-1 py-1 text-[10px]"
          :class="route.path.startsWith(item.to) ? 'text-primary-600' : 'text-gray-400'"
        >
          <!-- Calendar icon (しおり) -->
          <svg
            v-if="item.icon === 'calendar'"
            class="h-5 w-5"
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
          <!-- Photo icon (アルバム) -->
          <svg
            v-if="item.icon === 'photo'"
            class="h-5 w-5"
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
            v-if="item.icon === 'board'"
            class="h-5 w-5"
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
            v-if="item.icon === 'packing'"
            class="h-5 w-5"
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
          <!-- Expenses icon (費用) -->
          <svg
            v-if="item.icon === 'expenses'"
            class="h-5 w-5"
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
          <span>{{ item.label }}</span>
        </NuxtLink>
      </div>
    </nav>
  </div>
</template>

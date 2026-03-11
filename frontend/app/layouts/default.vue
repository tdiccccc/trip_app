<script setup lang="ts">
const { isAuthenticated, logout } = useAuth()
const route = useRoute()

// Extract tripId from route if available
const tripId = computed(() => {
  const params = route.params
  return (params.tripId as string) ?? null
})

// Navigation items scoped to current trip
const navItems = computed(() => {
  if (!tripId.value) return []
  const base = `/trips/${tripId.value}`
  return [
    { to: `${base}/itinerary`, label: 'しおり', icon: 'calendar' },
    { to: `${base}/album`, label: 'アルバム', icon: 'photo' },
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
            Ise Trip
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
          class="flex flex-col items-center gap-0.5 px-4 py-1 text-xs"
          :class="route.path.startsWith(item.to) ? 'text-primary-600' : 'text-gray-400'"
        >
          <!-- Calendar icon -->
          <svg
            v-if="item.icon === 'calendar'"
            class="h-6 w-6"
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
          <!-- Photo icon -->
          <svg
            v-if="item.icon === 'photo'"
            class="h-6 w-6"
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
          <span>{{ item.label }}</span>
        </NuxtLink>
      </div>
    </nav>
  </div>
</template>

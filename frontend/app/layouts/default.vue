<script setup lang="ts">
const { isAuthenticated, logout } = useAuth()
const route = useRoute()

const navItems = [
  { to: '/itinerary', label: 'しおり', icon: 'calendar' },
  { to: '/album', label: 'アルバム', icon: 'photo' },
]
</script>

<template>
  <div class="flex min-h-dvh flex-col bg-amber-50/30">
    <!-- Header -->
    <header class="sticky top-0 z-40 border-b border-amber-100 bg-white/90 backdrop-blur-sm">
      <div class="flex items-center justify-between px-4 py-3">
        <NuxtLink
          to="/"
          class="text-lg font-bold text-amber-700"
        >
          Ise Trip
        </NuxtLink>
        <button
          v-if="isAuthenticated"
          class="text-sm text-gray-500 hover:text-gray-700"
          @click="logout"
        >
          Logout
        </button>
      </div>
    </header>

    <!-- Main content -->
    <main class="flex-1 px-4 py-4">
      <slot />
    </main>

    <!-- Bottom navigation -->
    <nav
      v-if="isAuthenticated"
      class="sticky bottom-0 z-40 border-t border-amber-100 bg-white/95 backdrop-blur-sm"
    >
      <div class="flex justify-around py-2">
        <NuxtLink
          v-for="item in navItems"
          :key="item.to"
          :to="item.to"
          class="flex flex-col items-center gap-0.5 px-4 py-1 text-xs"
          :class="route.path.startsWith(item.to) ? 'text-amber-600' : 'text-gray-400'"
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

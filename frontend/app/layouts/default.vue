<script setup lang="ts">
</script>

<template>
  <div class="min-h-screen bg-gray-50 pb-16">
    <!-- Header -->
    <header class="sticky top-0 z-50 bg-white shadow-sm">
      <div class="flex items-center justify-between px-4 py-3">
        <NuxtLink to="/" class="text-lg font-bold text-rose-600">
          Ise Trip
        </NuxtLink>
      </div>
    </header>

    <!-- Main Content -->
    <main class="px-4 py-4">
      <slot />
    </main>

    <!-- Bottom Navigation -->
    <nav class="fixed bottom-0 left-0 right-0 z-50 border-t border-gray-200 bg-white">
      <div class="flex justify-around py-2">
        <NuxtLink to="/itinerary" class="flex flex-col items-center text-xs text-gray-600 hover:text-rose-600">
          <span class="text-lg">&#128203;</span>
          <span>しおり</span>
        </NuxtLink>
        <NuxtLink to="/album" class="flex flex-col items-center text-xs text-gray-600 hover:text-rose-600">
          <span class="text-lg">&#128247;</span>
          <span>アルバム</span>
        </NuxtLink>
        <NuxtLink to="/board" class="flex flex-col items-center text-xs text-gray-600 hover:text-rose-600">
          <span class="text-lg">&#128172;</span>
          <span>掲示板</span>
        </NuxtLink>
        <NuxtLink to="/packing" class="flex flex-col items-center text-xs text-gray-600 hover:text-rose-600">
          <span class="text-lg">&#127890;</span>
          <span>持ち物</span>
        </NuxtLink>
        <NuxtLink to="/expenses" class="flex flex-col items-center text-xs text-gray-600 hover:text-rose-600">
          <span class="text-lg">&#128176;</span>
          <span>費用</span>
        </NuxtLink>
      </div>
    </nav>
  </div>
</template>

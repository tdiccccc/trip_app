<script setup lang="ts">
import type { Spot } from '~/types/spot'

defineProps<{
  spot: Spot
}>()

const categoryLabels: Record<string, string> = {
  sightseeing: '\u89B3\u5149',
  food: '\u30B0\u30EB\u30E1',
  hotel: '\u5BBF\u6CCA',
  other: '\u305D\u306E\u4ED6',
}

const categoryColors: Record<string, string> = {
  sightseeing: 'bg-rose-100 text-rose-700',
  food: 'bg-orange-100 text-orange-700',
  hotel: 'bg-blue-100 text-blue-700',
  other: 'bg-gray-100 text-gray-700',
}
</script>

<template>
  <NuxtLink
    :to="`/spots/${spot.id}`"
    class="block rounded-2xl bg-white shadow-sm transition-shadow hover:shadow-md"
  >
    <!-- Image -->
    <div class="relative h-36 overflow-hidden rounded-t-2xl bg-gray-100">
      <img
        v-if="spot.image_url"
        :src="spot.image_url"
        :alt="spot.name"
        class="h-full w-full object-cover"
      >
      <div
        v-else
        class="flex h-full items-center justify-center text-3xl text-gray-300"
      >
        <svg
          class="h-12 w-12"
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
      </div>

      <!-- Category badge -->
      <span
        class="absolute left-2 top-2 rounded-full px-2.5 py-0.5 text-xs font-medium"
        :class="categoryColors[spot.category] ?? 'bg-gray-100 text-gray-700'"
      >
        {{ categoryLabels[spot.category] ?? spot.category }}
      </span>
    </div>

    <!-- Info -->
    <div class="p-3">
      <h3 class="text-base font-semibold text-gray-800">
        {{ spot.name }}
      </h3>
      <p
        v-if="spot.address"
        class="mt-0.5 truncate text-sm text-gray-500"
      >
        {{ spot.address }}
      </p>
    </div>
  </NuxtLink>
</template>

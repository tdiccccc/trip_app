<script setup lang="ts">
import type { Trip } from '~/types/trip'

const props = defineProps<{
  trip: Trip
}>()

const formatDateRange = (startDate: string, endDate: string) => {
  const start = new Date(startDate + 'T00:00:00')
  const end = new Date(endDate + 'T00:00:00')
  const startStr = `${start.getFullYear()}.${String(start.getMonth() + 1).padStart(2, '0')}.${String(start.getDate()).padStart(2, '0')}`
  const endStr = `${String(end.getMonth() + 1).padStart(2, '0')}.${String(end.getDate()).padStart(2, '0')}`
  return `${startStr} - ${endStr}`
}

const memberNames = computed(() => {
  return props.trip.members.map(m => m.name).join(', ')
})
</script>

<template>
  <NuxtLink
    :to="`/trips/${trip.id}`"
    class="block rounded-2xl bg-white shadow-sm transition-shadow hover:shadow-md"
  >
    <!-- Cover image -->
    <div class="h-32 overflow-hidden rounded-t-2xl bg-gradient-to-br from-primary-100 to-primary-200">
      <img
        v-if="trip.cover_image_url"
        :src="trip.cover_image_url"
        :alt="trip.title"
        class="h-full w-full object-cover"
      >
      <div
        v-else
        class="flex h-full items-center justify-center"
      >
        <svg
          class="h-12 w-12 text-primary-300"
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
    </div>

    <!-- Trip info -->
    <div class="p-4">
      <h2 class="text-lg font-bold text-gray-800">
        {{ trip.title }}
      </h2>
      <p
        v-if="trip.destination"
        class="mt-0.5 text-sm text-gray-500"
      >
        {{ trip.destination }}
      </p>
      <div class="mt-2 flex items-center justify-between">
        <span class="text-xs text-gray-400">
          {{ formatDateRange(trip.start_date, trip.end_date) }}
        </span>
        <span class="text-xs text-primary-600">
          {{ memberNames }}
        </span>
      </div>
    </div>
  </NuxtLink>
</template>

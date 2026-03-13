<script setup lang="ts">
import type { Trip } from '~/types/trip'

definePageMeta({
  middleware: ['auth'],
})

useHead({
  title: '旅行一覧 - Trip App',
})

const { fetchTrips } = useTrips()
const { data: response } = fetchTrips()

const trips = computed<Trip[]>(() => response.value?.data ?? [])
</script>

<template>
  <div>
    <!-- Header -->
    <div class="mb-6 flex items-center justify-between">
      <h1 class="text-xl font-bold text-gray-800">
        旅行一覧
      </h1>
      <NuxtLink
        to="/trips/new"
        class="rounded-xl bg-primary-500 px-4 py-2 text-sm font-semibold text-white transition-colors hover:bg-primary-600"
      >
        新しい旅行
      </NuxtLink>
    </div>

    <!-- Trip cards -->
    <div
      v-if="trips.length > 0"
      class="space-y-4"
    >
      <TripCard
        v-for="trip in trips"
        :key="trip.id"
        :trip="trip"
      />
    </div>

    <!-- Empty state -->
    <div
      v-else
      class="py-16 text-center"
    >
      <svg
        class="mx-auto mb-4 h-16 w-16 text-gray-300"
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
      <p class="mb-4 text-sm text-gray-400">
        まだ旅行がありません
      </p>
      <NuxtLink
        to="/trips/new"
        class="inline-block rounded-xl bg-primary-500 px-6 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-primary-600"
      >
        最初の旅行を作成
      </NuxtLink>
    </div>
  </div>
</template>

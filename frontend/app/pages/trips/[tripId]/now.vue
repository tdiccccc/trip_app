<script setup lang="ts">
import type { Spot } from '~/types/spot'
import type { ItineraryItem } from '~/types/itinerary'
import { haversineDistance } from '~/utils/distance'

definePageMeta({
  middleware: ['auth'],
})

useHead({
  title: '今どこ - Trip App',
})

const route = useRoute()
const tripId = route.params.tripId as string

// Data fetching
const { fetchSpots } = useSpots(tripId)
const { data: spotsResponse } = fetchSpots()

const { fetchTrip } = useTrips()
const { data: tripResponse } = fetchTrip(tripId)
const trip = computed(() => tripResponse.value?.data ?? null)

const spots = computed<Spot[]>(() => spotsResponse.value?.data ?? [])

// Today's date in YYYY-MM-DD (JST)
const today = computed(() => {
  const now = new Date()
  const jst = new Date(now.getTime() + (9 * 60 - now.getTimezoneOffset()) * 60000)
  return jst.toISOString().slice(0, 10)
})

const { fetchItems } = useItinerary(tripId)
const { data: itineraryResponse } = fetchItems(today)

const todayItems = computed<ItineraryItem[]>(() => {
  if (!itineraryResponse.value?.data) return []
  return [...itineraryResponse.value.data].sort((a, b) => a.sort_order - b.sort_order)
})

// Geolocation
const { coords, error: geoError, isLoading: geoLoading, lastUpdated } = useGeolocation()

// Spots with coordinates
const geoSpots = computed(() =>
  spots.value.filter((s): s is Spot & { latitude: number; longitude: number } =>
    s.latitude !== null && s.longitude !== null,
  ),
)

// Distance to each spot
const spotsWithDistance = computed(() => {
  if (!coords.value) return []
  return geoSpots.value.map(spot => ({
    spot,
    distance: haversineDistance(
      coords.value!.latitude, coords.value!.longitude,
      spot.latitude, spot.longitude,
    ),
  })).sort((a, b) => a.distance - b.distance)
})

// Nearest spot
const nearest = computed(() => spotsWithDistance.value[0] ?? null)

// Nearest spot label
const nearestLabel = computed(() => {
  if (!nearest.value) return null
  const d = nearest.value.distance
  if (d <= 200) return 'にいます'
  if (d <= 2000) return `の近く（約${Math.round(d)}m）`
  return null
})

const nearestDistanceText = computed(() => {
  if (!nearest.value) return ''
  const d = nearest.value.distance
  if (d <= 200) return ''
  if (d <= 2000) return ''
  if (d >= 1000) return `約${(d / 1000).toFixed(1)}km`
  return `約${Math.round(d)}m`
})

// Current position in itinerary: find the nearest spot's itinerary item
const currentItineraryIndex = computed(() => {
  if (!nearest.value || todayItems.value.length === 0) return -1
  return todayItems.value.findIndex(item => item.spot_id === nearest.value!.spot.id)
})

// Next spot from itinerary
const nextItineraryItem = computed(() => {
  const idx = currentItineraryIndex.value
  if (idx === -1) {
    // If we can't find current position, return first incomplete item
    return todayItems.value[0] ?? null
  }
  // Return the next item after current
  return todayItems.value[idx + 1] ?? null
})

const nextSpot = computed(() => {
  if (!nextItineraryItem.value?.spot_id) return null
  return geoSpots.value.find(s => s.id === nextItineraryItem.value!.spot_id) ?? null
})

const nextSpotDistance = computed(() => {
  if (!coords.value || !nextSpot.value) return null
  const d = haversineDistance(
    coords.value.latitude, coords.value.longitude,
    nextSpot.value.latitude, nextSpot.value.longitude,
  )
  if (d >= 1000) return `約${(d / 1000).toFixed(1)}km`
  return `約${Math.round(d)}m`
})

const nextSpotGoogleMapsUrl = computed(() => {
  if (!nextSpot.value) return null
  return `https://www.google.com/maps/dir/?api=1&destination=${nextSpot.value.latitude},${nextSpot.value.longitude}`
})

// Itinerary item status
const getItemStatus = (item: ItineraryItem, index: number): 'done' | 'current' | 'upcoming' => {
  const idx = currentItineraryIndex.value
  if (idx === -1) return 'upcoming'
  if (index < idx) return 'done'
  if (index === idx) return 'current'
  return 'upcoming'
}

// Format time display
const formatTime = (time: string | null) => {
  if (!time) return ''
  return time.slice(0, 5)
}

const formatLastUpdated = computed(() => {
  if (!lastUpdated.value) return ''
  const h = String(lastUpdated.value.getHours()).padStart(2, '0')
  const m = String(lastUpdated.value.getMinutes()).padStart(2, '0')
  return `${h}:${m}`
})

// Spot name by id
const spotName = (spotId: number | null): string | null => {
  if (!spotId) return null
  return spots.value.find(s => s.id === spotId)?.name ?? null
}
</script>

<template>
  <div class="flex flex-col gap-4 py-6">
    <!-- Header -->
    <div class="flex items-center gap-3">
      <NuxtLink
        :to="`/trips/${tripId}`"
        class="flex h-8 w-8 items-center justify-center rounded-full text-gray-500 hover:bg-gray-100"
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
      <h1 class="text-xl font-bold text-gray-800">
        今どこ
      </h1>
    </div>

    <!-- GPS Error -->
    <div
      v-if="geoError"
      class="rounded-2xl bg-red-50 p-5 text-center shadow-sm"
    >
      <svg
        class="mx-auto mb-2 h-8 w-8 text-red-400"
        fill="none"
        stroke="currentColor"
        viewBox="0 0 24 24"
      >
        <path
          stroke-linecap="round"
          stroke-linejoin="round"
          stroke-width="1.5"
          d="M12 9v2m0 4h.01M12 2a10 10 0 100 20 10 10 0 000-20z"
        />
      </svg>
      <p class="text-sm font-medium text-red-600">
        {{ geoError }}
      </p>
    </div>

    <!-- Loading -->
    <div
      v-else-if="geoLoading"
      class="rounded-2xl bg-white p-8 text-center shadow-sm"
    >
      <div class="mx-auto mb-3 h-8 w-8 animate-spin rounded-full border-2 border-primary-500 border-t-transparent" />
      <p class="text-sm text-gray-500">
        現在地を取得しています...
      </p>
    </div>

    <!-- Main content -->
    <template v-else-if="coords">
      <!-- Current location card -->
      <div class="rounded-2xl bg-white p-5 shadow-sm">
        <p class="mb-2 text-xs font-medium text-gray-400">
          現在地
        </p>
        <div
          v-if="nearest"
          class="flex items-start gap-3"
        >
          <svg
            class="mt-0.5 h-5 w-5 shrink-0 text-primary-500"
            fill="currentColor"
            viewBox="0 0 24 24"
          >
            <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5a2.5 2.5 0 010-5 2.5 2.5 0 010 5z" />
          </svg>
          <div>
            <p class="text-base font-semibold text-gray-800">
              {{ nearest.spot.name }}
              <span
                v-if="nearestLabel"
                class="font-normal text-gray-600"
              >{{ nearestLabel }}</span>
            </p>
            <p
              v-if="nearestDistanceText"
              class="mt-0.5 text-sm text-gray-500"
            >
              {{ nearest.spot.name }}まで {{ nearestDistanceText }}
            </p>
          </div>
        </div>
        <div
          v-else
          class="text-sm text-gray-500"
        >
          近くにスポットが見つかりません
        </div>
        <p
          v-if="formatLastUpdated"
          class="mt-2 text-right text-xs text-gray-400"
        >
          更新: {{ formatLastUpdated }}
        </p>
      </div>

      <!-- Next spot card -->
      <div
        v-if="nextItineraryItem"
        class="rounded-2xl bg-white p-5 shadow-sm"
      >
        <p class="mb-2 text-xs font-medium text-gray-400">
          次のスポット
        </p>
        <div class="flex items-start gap-3">
          <svg
            class="mt-0.5 h-5 w-5 shrink-0 text-primary-500"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M13 7l5 5m0 0l-5 5m5-5H6"
            />
          </svg>
          <div class="flex-1">
            <p class="text-base font-semibold text-gray-800">
              {{ nextItineraryItem.title }}
            </p>
            <p
              v-if="nextSpotDistance"
              class="mt-0.5 text-sm text-gray-500"
            >
              あと {{ nextSpotDistance }}
            </p>
            <a
              v-if="nextSpotGoogleMapsUrl"
              :href="nextSpotGoogleMapsUrl"
              target="_blank"
              rel="noopener noreferrer"
              class="mt-2 inline-flex items-center gap-1 rounded-lg bg-primary-50 px-3 py-1.5 text-xs font-medium text-primary-600 transition-colors hover:bg-primary-100"
            >
              <svg
                class="h-3.5 w-3.5"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"
                />
              </svg>
              Google Maps で開く
            </a>
          </div>
        </div>
      </div>

      <!-- Today's itinerary card -->
      <div
        v-if="todayItems.length > 0"
        class="rounded-2xl bg-white p-5 shadow-sm"
      >
        <p class="mb-3 text-xs font-medium text-gray-400">
          今日の行程
        </p>
        <div class="flex flex-col gap-0">
          <div
            v-for="(item, index) in todayItems"
            :key="item.id"
            class="flex items-start gap-3 py-2"
          >
            <!-- Status icon -->
            <div class="flex w-5 shrink-0 justify-center pt-0.5">
              <!-- Done -->
              <svg
                v-if="getItemStatus(item, index) === 'done'"
                class="h-4 w-4 text-primary-500"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2.5"
                  d="M5 13l4 4L19 7"
                />
              </svg>
              <!-- Current -->
              <svg
                v-else-if="getItemStatus(item, index) === 'current'"
                class="h-4 w-4 text-primary-500"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M13 7l5 5m0 0l-5 5m5-5H6"
                />
              </svg>
              <!-- Upcoming -->
              <div
                v-else
                class="mt-0.5 h-3 w-3 rounded-full border-2 border-gray-300"
              />
            </div>
            <!-- Content -->
            <div class="flex-1 min-w-0">
              <p
                class="text-sm"
                :class="{
                  'text-gray-400 line-through': getItemStatus(item, index) === 'done',
                  'font-semibold text-primary-600': getItemStatus(item, index) === 'current',
                  'text-gray-600': getItemStatus(item, index) === 'upcoming',
                }"
              >
                <span
                  v-if="item.start_time"
                  class="mr-1.5"
                >{{ formatTime(item.start_time) }}</span>
                <span>{{ item.title }}</span>
                <span
                  v-if="item.spot_id && spotName(item.spot_id)"
                  class="ml-1 text-xs text-gray-400"
                >{{ spotName(item.spot_id) }}</span>
              </p>
            </div>
          </div>
        </div>
      </div>

      <!-- No itinerary -->
      <div
        v-else
        class="rounded-2xl bg-white p-5 text-center shadow-sm"
      >
        <p class="text-sm text-gray-400">
          今日の行程はまだ登録されていません
        </p>
        <NuxtLink
          :to="`/trips/${tripId}/itinerary`"
          class="mt-2 inline-block text-sm font-medium text-primary-500"
        >
          しおりを作成する
        </NuxtLink>
      </div>
    </template>
  </div>
</template>

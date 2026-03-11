<script setup lang="ts">
import type { ItineraryItem } from '~/types/itinerary'

const props = defineProps<{
  item: ItineraryItem
  isNow?: boolean
}>()

const emit = defineEmits<{
  edit: [item: ItineraryItem]
  delete: [id: number]
}>()

const timeRange = computed(() => {
  if (!props.item.start_time) return ''
  const start = props.item.start_time.slice(0, 5)
  const end = props.item.end_time ? props.item.end_time.slice(0, 5) : ''
  return end ? `${start} - ${end}` : start
})
</script>

<template>
  <div
    class="relative flex gap-3 py-3 pl-4"
    :class="isNow ? 'bg-primary-50 rounded-xl border border-primary-300' : ''"
  >
    <!-- Timeline dot -->
    <div class="flex flex-col items-center pt-1">
      <div
        class="h-3 w-3 rounded-full"
        :class="isNow ? 'bg-primary-500 ring-4 ring-primary-200' : 'bg-gray-300'"
      />
      <div class="mt-1 h-full w-0.5 bg-gray-200" />
    </div>

    <!-- Content -->
    <div class="min-w-0 flex-1 pr-3">
      <div class="flex items-start justify-between gap-2">
        <div class="min-w-0 flex-1">
          <!-- Time -->
          <p
            v-if="timeRange"
            class="text-xs font-medium text-primary-600"
          >
            {{ timeRange }}
            <span
              v-if="isNow"
              class="ml-1 inline-block rounded-full bg-primary-500 px-2 py-0.5 text-xs font-bold text-white"
            >
              現在
            </span>
          </p>

          <!-- Title -->
          <h3 class="mt-0.5 text-base font-semibold text-gray-800">
            {{ item.title }}
          </h3>

          <!-- Memo -->
          <p
            v-if="item.memo"
            class="mt-1 text-sm text-gray-500"
          >
            {{ item.memo }}
          </p>

          <!-- Transport -->
          <div
            v-if="item.transport"
            class="mt-1"
          >
            <TransportIcon :transport="item.transport" />
          </div>
        </div>

        <!-- Actions -->
        <div class="flex shrink-0 gap-1">
          <button
            class="rounded-lg p-1.5 text-gray-400 hover:bg-gray-100 hover:text-gray-600"
            aria-label="編集"
            @click="emit('edit', item)"
          >
            <svg
              class="h-4 w-4"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"
              />
            </svg>
          </button>
          <button
            class="rounded-lg p-1.5 text-gray-400 hover:bg-red-50 hover:text-red-500"
            aria-label="削除"
            @click="emit('delete', item.id)"
          >
            <svg
              class="h-4 w-4"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
              />
            </svg>
          </button>
        </div>
      </div>

      <!-- Spot link -->
      <NuxtLink
        v-if="item.spot_id"
        :to="`/spots/${item.spot_id}`"
        class="mt-1 inline-block text-xs text-primary-600 underline"
      >
        スポット詳細を見る
      </NuxtLink>
    </div>
  </div>
</template>

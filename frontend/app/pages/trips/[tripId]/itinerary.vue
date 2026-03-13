<script setup lang="ts">
import type { ItineraryItem, CreateItineraryItemInput } from '~/types/itinerary'
import type { Spot } from '~/types/spot'

definePageMeta({
  middleware: ['auth'],
})

useHead({
  title: 'しおり - Trip App',
})

const route = useRoute()
const tripId = route.params.tripId as string

const { fetchTrip } = useTrips()
const { data: tripResponse } = fetchTrip(tripId)
const trip = computed(() => tripResponse.value?.data ?? null)

const { fetchItems, createItem, updateItem, deleteItem, reorderItems } = useItinerary(tripId)

// View mode toggle (list / map)
const viewMode = ref<'list' | 'map'>('list')

// Fetch spots for map view
const { fetchSpots } = useSpots(tripId)
const { data: spotsResponse } = fetchSpots()
const spots = computed<Spot[]>(() => {
  const res = spotsResponse.value as { data?: Spot[] } | null
  return res?.data ?? []
})

// Generate trip dates dynamically from trip data
const tripDates = computed<string[]>(() => {
  if (!trip.value) return []
  const dates: string[] = []
  const start = new Date(trip.value.start_date + 'T00:00:00')
  const end = new Date(trip.value.end_date + 'T00:00:00')
  const current = new Date(start)
  while (current <= end) {
    dates.push(current.toISOString().slice(0, 10))
    current.setDate(current.getDate() + 1)
  }
  return dates
})

const selectedDate = ref('')

// Set initial selected date when trip data loads
watch(tripDates, (dates) => {
  if (dates.length > 0 && !selectedDate.value) {
    selectedDate.value = dates[0]
  }
}, { immediate: true })

// Fetch items for selected date (reactively watches selectedDate)
const { data: response, refresh } = fetchItems(selectedDate)

const items = computed(() => {
  if (!response.value?.data) return []
  return [...response.value.data].sort((a, b) => a.sort_order - b.sort_order)
})

// Current time highlight
const now = ref(new Date())
let timer: ReturnType<typeof setInterval> | null = null

onMounted(() => {
  timer = setInterval(() => {
    now.value = new Date()
  }, 60000)
})

onUnmounted(() => {
  if (timer) clearInterval(timer)
})

const isCurrentItem = (item: ItineraryItem) => {
  if (!item.start_time || !item.end_time) return false

  const today = now.value.toISOString().slice(0, 10)
  if (item.date !== today) return false

  const currentMinutes = now.value.getHours() * 60 + now.value.getMinutes()
  const [startH, startM] = item.start_time.split(':').map(Number)
  const [endH, endM] = item.end_time.split(':').map(Number)
  const startMinutes = startH * 60 + startM
  const endMinutes = endH * 60 + endM

  return currentMinutes >= startMinutes && currentMinutes <= endMinutes
}

// Form modal
const showForm = ref(false)
const editingItem = ref<ItineraryItem | null>(null)
const isSubmitting = ref(false)

const openAddForm = () => {
  editingItem.value = null
  showForm.value = true
}

const openEditForm = (item: ItineraryItem) => {
  editingItem.value = item
  showForm.value = true
}

const closeForm = () => {
  showForm.value = false
  editingItem.value = null
}

const handleSubmit = async (data: CreateItineraryItemInput) => {
  isSubmitting.value = true
  try {
    if (editingItem.value) {
      await updateItem(editingItem.value.id, data)
    } else {
      await createItem({
        ...data,
        date: data.date || selectedDate.value,
      })
    }
    closeForm()
    await refresh()
  } catch {
    // Error handling could be improved with toast
  } finally {
    isSubmitting.value = false
  }
}

const handleDelete = async (id: number) => {
  if (!confirm('この予定を削除しますか？')) return
  try {
    await deleteItem(id)
    await refresh()
  } catch {
    // Error handling
  }
}

const handleMove = async (id: number, direction: 'up' | 'down') => {
  const list = items.value
  const idx = list.findIndex(item => item.id === id)
  if (idx < 0) return

  const swapIdx = direction === 'up' ? idx - 1 : idx + 1
  if (swapIdx < 0 || swapIdx >= list.length) return

  const reordered = list.map((item, i) => ({
    id: item.id,
    sort_order: i === idx
      ? list[swapIdx].sort_order
      : i === swapIdx
        ? list[idx].sort_order
        : item.sort_order,
  }))

  try {
    await reorderItems(reordered)
    await refresh()
  } catch {
    // Error handling
  }
}

const formatDateLabel = (date: string) => {
  const d = new Date(date + 'T00:00:00')
  const month = d.getMonth() + 1
  const day = d.getDate()
  const weekday = ['日', '月', '火', '水', '木', '金', '土'][d.getDay()]
  return `${month}/${day}(${weekday})`
}

// Map spot list for display below the map
const mapSpotList = computed(() => {
  const sorted = [...items.value].sort((a, b) => a.sort_order - b.sort_order)
  const result: { order: number; spot: { name: string }; item: ItineraryItem }[] = []
  let order = 1

  for (const item of sorted) {
    if (!item.spot_id) continue
    const spot = spots.value.find((s: Spot) => s.id === item.spot_id)
    if (!spot || spot.latitude === null || spot.longitude === null) continue
    result.push({ order: order++, spot, item })
  }

  return result
})

const formInitialData = computed(() => {
  if (!editingItem.value) {
    return { date: selectedDate.value }
  }
  return {
    title: editingItem.value.title,
    date: editingItem.value.date,
    start_time: editingItem.value.start_time,
    end_time: editingItem.value.end_time,
    memo: editingItem.value.memo,
    transport: editingItem.value.transport,
    spot_id: editingItem.value.spot_id,
  }
})
</script>

<template>
  <div>
    <!-- Date tabs -->
    <div class="mb-4 flex gap-2">
      <button
        v-for="date in tripDates"
        :key="date"
        class="rounded-full px-4 py-2 text-sm font-medium transition-colors"
        :class="selectedDate === date
          ? 'bg-primary-500 text-white shadow-sm'
          : 'bg-white text-gray-600 hover:bg-primary-50'"
        @click="selectedDate = date"
      >
        {{ formatDateLabel(date) }}
      </button>
    </div>

    <!-- View mode tabs -->
    <div class="mb-4 flex rounded-lg bg-gray-100 p-1">
      <button
        class="flex-1 rounded-md px-3 py-1.5 text-sm font-medium transition-colors"
        :class="viewMode === 'list'
          ? 'bg-white text-gray-800 shadow-sm'
          : 'text-gray-500 hover:text-gray-700'"
        @click="viewMode = 'list'"
      >
        リスト
      </button>
      <button
        class="flex-1 rounded-md px-3 py-1.5 text-sm font-medium transition-colors"
        :class="viewMode === 'map'
          ? 'bg-white text-gray-800 shadow-sm'
          : 'text-gray-500 hover:text-gray-700'"
        @click="viewMode = 'map'"
      >
        マップ
      </button>
    </div>

    <!-- List view -->
    <template v-if="viewMode === 'list'">
      <!-- Timeline -->
      <div
        v-if="items.length > 0"
        class="space-y-1"
      >
        <TimelineItem
          v-for="(item, index) in items"
          :key="item.id"
          :item="item"
          :is-now="isCurrentItem(item)"
          :is-first="index === 0"
          :is-last="index === items.length - 1"
          :trip-id="tripId"
          @edit="openEditForm"
          @delete="handleDelete"
          @move-up="handleMove($event, 'up')"
          @move-down="handleMove($event, 'down')"
        />
      </div>

      <!-- Empty state -->
      <div
        v-else
        class="py-16 text-center"
      >
        <p class="text-sm text-gray-400">
          この日の予定はまだありません
        </p>
      </div>
    </template>

    <!-- Map view -->
    <template v-if="viewMode === 'map'">
      <RouteMap
        :spots="spots"
        :itinerary-items="items"
      />

      <!-- Spot list below map -->
      <div
        v-if="mapSpotList.length > 0"
        class="mt-4 space-y-2"
      >
        <div
          v-for="entry in mapSpotList"
          :key="entry.item.id"
          class="flex items-center gap-3 rounded-lg bg-white px-3 py-2 shadow-sm"
        >
          <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-indigo-500 text-xs font-bold text-white">
            {{ entry.order }}
          </span>
          <div class="min-w-0 flex-1">
            <p class="truncate text-sm font-medium text-gray-800">
              {{ entry.spot.name }}
            </p>
            <p
              v-if="entry.item.start_time"
              class="text-xs text-gray-500"
            >
              {{ entry.item.start_time.slice(0, 5) }}
              <template v-if="entry.item.end_time">
                - {{ entry.item.end_time.slice(0, 5) }}
              </template>
            </p>
          </div>
        </div>
      </div>
    </template>

    <!-- Add button -->
    <div class="fixed bottom-20 right-4 z-30">
      <button
        class="flex h-14 w-14 items-center justify-center rounded-full bg-primary-500 text-white shadow-lg transition-transform hover:scale-105 active:scale-95"
        @click="openAddForm"
      >
        <svg
          class="h-7 w-7"
          fill="none"
          stroke="currentColor"
          viewBox="0 0 24 24"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M12 6v6m0 0v6m0-6h6m-6 0H6"
          />
        </svg>
      </button>
    </div>

    <!-- Form modal -->
    <Teleport to="body">
      <div
        v-if="showForm"
        class="fixed inset-0 z-50 flex items-end justify-center"
      >
        <!-- Backdrop -->
        <div
          class="absolute inset-0 bg-black/40"
          @click="closeForm"
        />
        <!-- Sheet -->
        <div class="relative w-full max-w-lg rounded-t-2xl bg-white pb-safe">
          <div class="flex items-center justify-between border-b border-gray-100 px-4 py-3">
            <h2 class="text-base font-semibold text-gray-800">
              {{ editingItem ? '予定を編集' : '予定を追加' }}
            </h2>
            <button
              class="text-gray-400 hover:text-gray-600"
              @click="closeForm"
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
                  d="M6 18L18 6M6 6l12 12"
                />
              </svg>
            </button>
          </div>
          <TimelineForm
            :key="editingItem?.id ?? 'new'"
            :initial-data="formInitialData"
            :is-edit="!!editingItem"
            @submit="handleSubmit"
            @cancel="closeForm"
          />
        </div>
      </div>
    </Teleport>
  </div>
</template>

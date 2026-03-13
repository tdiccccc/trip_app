<script setup lang="ts">
const route = useRoute()
const tripId = route.params.tripId as string
const spotId = route.params.id as string

definePageMeta({
  middleware: ['auth'],
})

const { fetchSpot, createMemo } = useSpots(tripId)
const { data: response, refresh } = fetchSpot(spotId)

const spot = computed(() => response.value?.data ?? null)

useHead({
  title: computed(() => spot.value ? `${spot.value.name} - Trip App` : 'スポット詳細 - Trip App'),
})

// Memo form
const newMemo = ref('')
const isSendingMemo = ref(false)

const handleSendMemo = async () => {
  if (!spot.value || !newMemo.value.trim()) return
  isSendingMemo.value = true
  try {
    await createMemo(spot.value.id, newMemo.value.trim())
    newMemo.value = ''
    await refresh()
  } catch {
    // Error handling
  } finally {
    isSendingMemo.value = false
  }
}

const categoryLabels: Record<string, string> = {
  sightseeing: '観光',
  food: 'グルメ',
  hotel: '宿泊',
  other: 'その他',
}

const categoryColors: Record<string, string> = {
  sightseeing: 'bg-rose-100 text-rose-700',
  food: 'bg-orange-100 text-orange-700',
  hotel: 'bg-blue-100 text-blue-700',
  other: 'bg-gray-100 text-gray-700',
}
</script>

<template>
  <div v-if="spot">
    <!-- Hero image -->
    <div class="-mx-4 -mt-4 mb-4 h-48 bg-gray-100">
      <img
        v-if="spot.image_url"
        :src="spot.image_url"
        :alt="spot.name"
        class="h-full w-full object-cover"
      >
      <div
        v-else
        class="flex h-full items-center justify-center"
      >
        <svg
          class="h-16 w-16 text-gray-300"
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

    <!-- Name & Category -->
    <div class="mb-4">
      <span
        class="mb-1 inline-block rounded-full px-2.5 py-0.5 text-xs font-medium"
        :class="categoryColors[spot.category] ?? 'bg-gray-100 text-gray-700'"
      >
        {{ categoryLabels[spot.category] ?? spot.category }}
      </span>
      <h1 class="text-xl font-bold text-gray-800">
        {{ spot.name }}
      </h1>
      <p
        v-if="spot.description"
        class="mt-1 text-sm text-gray-500"
      >
        {{ spot.description }}
      </p>
    </div>

    <!-- Info cards -->
    <div class="mb-6 space-y-2">
      <!-- Address -->
      <div
        v-if="spot.address"
        class="flex items-start gap-3 rounded-xl bg-white p-3 shadow-sm"
      >
        <svg
          class="mt-0.5 h-5 w-5 shrink-0 text-gray-400"
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
        <span class="text-sm text-gray-700">{{ spot.address }}</span>
      </div>

      <!-- Business hours -->
      <div
        v-if="spot.business_hours"
        class="flex items-start gap-3 rounded-xl bg-white p-3 shadow-sm"
      >
        <svg
          class="mt-0.5 h-5 w-5 shrink-0 text-gray-400"
          fill="none"
          stroke="currentColor"
          viewBox="0 0 24 24"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="1.5"
            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
          />
        </svg>
        <span class="text-sm text-gray-700">{{ spot.business_hours }}</span>
      </div>

      <!-- Price info -->
      <div
        v-if="spot.price_info"
        class="flex items-start gap-3 rounded-xl bg-white p-3 shadow-sm"
      >
        <svg
          class="mt-0.5 h-5 w-5 shrink-0 text-gray-400"
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
        <span class="text-sm text-gray-700">{{ spot.price_info }}</span>
      </div>

      <!-- Google Maps link -->
      <a
        v-if="spot.google_maps_url"
        :href="spot.google_maps_url"
        target="_blank"
        rel="noopener noreferrer"
        class="flex items-center gap-3 rounded-xl bg-primary-500 p-3 text-white shadow-sm transition-colors hover:bg-primary-600"
      >
        <svg
          class="h-5 w-5 shrink-0"
          fill="none"
          stroke="currentColor"
          viewBox="0 0 24 24"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="1.5"
            d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"
          />
        </svg>
        <span class="text-sm font-semibold">Google Maps で開く</span>
      </a>
    </div>

    <!-- Photos section -->
    <div
      v-if="spot.photos && spot.photos.length > 0"
      class="mb-6"
    >
      <h2 class="mb-3 text-base font-semibold text-gray-800">
        写真
      </h2>
      <PhotoGrid
        :photos="spot.photos"
        @select="() => {}"
      />
    </div>

    <!-- Memos section -->
    <div class="mb-6">
      <h2 class="mb-3 text-base font-semibold text-gray-800">
        メモ
      </h2>

      <!-- Memo list -->
      <div
        v-if="spot.memos && spot.memos.length > 0"
        class="mb-3 space-y-2"
      >
        <SpotMemoItem
          v-for="memo in spot.memos"
          :key="memo.id"
          :memo="memo"
        />
      </div>
      <p
        v-else
        class="mb-3 text-sm text-gray-400"
      >
        メモはまだありません
      </p>

      <!-- Memo form -->
      <form
        class="flex gap-2"
        @submit.prevent="handleSendMemo"
      >
        <input
          v-model="newMemo"
          type="text"
          placeholder="メモを追加..."
          class="flex-1 rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-400 focus:outline-none focus:ring-2 focus:ring-primary-200"
        >
        <button
          type="submit"
          :disabled="!newMemo.trim() || isSendingMemo"
          class="rounded-xl bg-primary-500 px-4 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-primary-600 disabled:cursor-not-allowed disabled:opacity-50"
        >
          送信
        </button>
      </form>
    </div>
  </div>

  <!-- Loading state -->
  <div
    v-else
    class="py-16 text-center"
  >
    <p class="text-sm text-gray-400">
      読み込み中...
    </p>
  </div>
</template>

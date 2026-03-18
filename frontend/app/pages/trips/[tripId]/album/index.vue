<script setup lang="ts">
import type { Photo } from '~/types/photo'
import type { Spot } from '~/types/spot'

definePageMeta({
  middleware: ['auth'],
})

useHead({
  title: 'アルバム - Trip App',
})

const route = useRoute()
const tripId = route.params.tripId as string

const { fetchPhotos, deletePhoto } = useAlbum(tripId)
const { fetchSpots } = useSpots(tripId)
const { updateTrip } = useTrips()

// Spot filter
const selectedSpotId = ref<number | undefined>(undefined)
const { data: spotsResponse } = fetchSpots()
const spots = computed<Spot[]>(() => spotsResponse.value?.data ?? [])

// Fetch photos (reactively watches selectedSpotId)
const photoParams = computed(() =>
  selectedSpotId.value ? { spot_id: selectedSpotId.value } : undefined,
)
const { data: photosResponse, refresh } = fetchPhotos(photoParams)
const photos = computed<Photo[]>(() => photosResponse.value?.data ?? [])

// Upload
const showUploader = ref(false)

const handleUploaded = async () => {
  showUploader.value = false
  await refresh()
}

// Lightbox
const selectedPhoto = ref<Photo | null>(null)

const closeLightbox = () => {
  selectedPhoto.value = null
}

const handleDeletePhoto = async () => {
  if (!selectedPhoto.value) return
  if (!confirm('この写真を削除しますか？')) return
  try {
    await deletePhoto(selectedPhoto.value.id)
    closeLightbox()
    await refresh()
  } catch {
    // Error handling
  }
}

// Cover image
const settingCover = ref(false)
const coverSetMessage = ref('')

const setAsCover = async () => {
  if (!selectedPhoto.value) return
  settingCover.value = true
  coverSetMessage.value = ''
  try {
    await updateTrip(tripId, { cover_image_url: selectedPhoto.value.storage_path })
    coverSetMessage.value = 'カバー画像に設定しました'
    setTimeout(() => {
      coverSetMessage.value = ''
    }, 2000)
  } catch {
    coverSetMessage.value = '設定に失敗しました'
    setTimeout(() => {
      coverSetMessage.value = ''
    }, 2000)
  } finally {
    settingCover.value = false
  }
}
</script>

<template>
  <div>
    <!-- Header area -->
    <div class="mb-4 flex items-center justify-between">
      <h1 class="text-xl font-bold text-gray-800">
        アルバム
      </h1>
      <div class="flex items-center gap-2">
        <NuxtLink
          :to="`/trips/${tripId}/album/slideshow`"
          class="rounded-xl border border-primary-300 px-3 py-2 text-sm font-medium text-primary-600 transition-colors hover:bg-primary-50"
        >
          スライドショー
        </NuxtLink>
        <button
          class="rounded-xl bg-primary-500 px-4 py-2 text-sm font-semibold text-white transition-colors hover:bg-primary-600"
          @click="showUploader = !showUploader"
        >
          {{ showUploader ? '閉じる' : 'アップロード' }}
        </button>
      </div>
    </div>

    <!-- Uploader -->
    <div
      v-if="showUploader"
      class="mb-4"
    >
      <PhotoUploader
        :trip-id="tripId"
        @uploaded="handleUploaded"
      />
    </div>

    <!-- Spot filter -->
    <div class="mb-4">
      <select
        v-model="selectedSpotId"
        class="w-full rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm focus:border-primary-400 focus:outline-none focus:ring-2 focus:ring-primary-200"
      >
        <option :value="undefined">
          すべてのスポット
        </option>
        <option
          v-for="spot in spots"
          :key="spot.id"
          :value="spot.id"
        >
          {{ spot.name }}
        </option>
      </select>
    </div>

    <!-- Photo grid -->
    <PhotoGrid
      :photos="photos"
      @select="selectedPhoto = $event"
    />

    <!-- Lightbox -->
    <Teleport to="body">
      <div
        v-if="selectedPhoto"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/90"
        @click.self="closeLightbox"
      >
        <!-- Close button -->
        <button
          class="absolute right-4 top-4 z-10 rounded-full bg-white/10 p-2 text-white backdrop-blur-sm"
          @click="closeLightbox"
        >
          <svg
            class="h-6 w-6"
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

        <!-- Image -->
        <img
          :src="selectedPhoto.storage_path"
          :alt="selectedPhoto.caption ?? selectedPhoto.original_filename"
          class="max-h-[80vh] max-w-[90vw] rounded-lg object-contain"
        >

        <!-- Toast message -->
        <div
          v-if="coverSetMessage"
          class="absolute top-16 left-1/2 z-20 -translate-x-1/2 rounded-xl bg-white/90 px-4 py-2 text-sm font-medium text-gray-800 shadow-lg backdrop-blur-sm"
        >
          {{ coverSetMessage }}
        </div>

        <!-- Bottom info bar -->
        <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/60 to-transparent p-4 pb-safe">
          <p
            v-if="selectedPhoto.caption"
            class="mb-2 text-center text-sm text-white"
          >
            {{ selectedPhoto.caption }}
          </p>
          <div class="flex justify-center gap-3">
            <button
              class="flex items-center gap-1.5 rounded-xl bg-white/20 px-4 py-2 text-sm text-white backdrop-blur-sm transition-colors hover:bg-white/30"
              :disabled="settingCover"
              @click="setAsCover"
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
                  d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"
                />
              </svg>
              {{ settingCover ? '設定中...' : 'カバーに設定' }}
            </button>
            <button
              class="rounded-xl bg-red-500/80 px-4 py-2 text-sm text-white backdrop-blur-sm transition-colors hover:bg-red-600"
              @click="handleDeletePhoto"
            >
              削除
            </button>
          </div>
        </div>
      </div>
    </Teleport>
  </div>
</template>

<script setup lang="ts">
import type { Photo } from '~/types/photo'

definePageMeta({
  layout: 'fullscreen',
  middleware: ['auth'],
})

useHead({
  title: 'スライドショー - Trip App',
})

const route = useRoute()
const tripId = route.params.tripId as string

const { fetchPhotos } = useAlbum(tripId)
const { data } = await fetchPhotos({ sort: 'taken_at', order: 'asc' })

const photos = computed<Photo[]>(() => data.value?.data ?? [])

const {
  currentPhoto,
  currentIndex,
  totalCount,
  isPlaying,
  intervalMs,
  transition,
  play,
  pause,
  next,
  prev,
  setSpeed,
  setTransition,
} = useSlideshow(photos)

const showControls = ref(true)
let hideTimer: ReturnType<typeof setTimeout> | null = null

const toggleControls = () => {
  showControls.value = !showControls.value
  if (showControls.value) {
    autoHideControls()
  }
}

const autoHideControls = () => {
  if (hideTimer) clearTimeout(hideTimer)
  hideTimer = setTimeout(() => {
    showControls.value = false
  }, 3000)
}

const handleClose = () => navigateTo(`/trips/${tripId}/album`)

onMounted(() => {
  if (photos.value.length > 0) {
    play()
  }
  autoHideControls()
})

onUnmounted(() => {
  if (hideTimer) clearTimeout(hideTimer)
})
</script>

<template>
  <div
    class="relative h-dvh w-full bg-black"
    @click="toggleControls"
  >
    <!-- Player -->
    <SlideshowPlayer
      :photo="currentPhoto"
      :transition="transition"
    />

    <!-- Controls (toggle on tap) -->
    <Transition name="fade">
      <SlideshowControls
        v-if="showControls && photos.length > 0"
        :is-playing="isPlaying"
        :current-index="currentIndex"
        :total-count="totalCount"
        :interval-ms="intervalMs"
        :transition="transition"
        @play="play"
        @pause="pause"
        @next="next"
        @prev="prev"
        @speed-change="setSpeed"
        @transition-change="setTransition"
        @close="handleClose"
        @click.stop
      />
    </Transition>

    <!-- Empty state -->
    <div
      v-if="photos.length === 0"
      class="absolute inset-0 flex items-center justify-center"
    >
      <div class="text-center text-white">
        <p class="text-lg">
          写真がありません
        </p>
        <button
          class="mt-4 rounded-full bg-white/20 px-6 py-2 text-white transition-colors hover:bg-white/30"
          @click="handleClose"
        >
          アルバムに戻る
        </button>
      </div>
    </div>
  </div>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>

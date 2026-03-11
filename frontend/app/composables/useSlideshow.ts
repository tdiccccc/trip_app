import type { Ref } from 'vue'
import type { Photo } from '~/types/photo'

export const useSlideshow = (photos: Ref<Photo[]>) => {
  const currentIndex = ref(0)
  const isPlaying = ref(false)
  const intervalMs = ref(4000)
  const transition = ref<'fade' | 'slide'>('fade')

  let timer: ReturnType<typeof setInterval> | null = null

  const currentPhoto = computed<Photo | null>(
    () => photos.value[currentIndex.value] ?? null,
  )
  const totalCount = computed(() => photos.value.length)
  const hasNext = computed(
    () => currentIndex.value < photos.value.length - 1,
  )
  const hasPrev = computed(() => currentIndex.value > 0)

  const next = () => {
    if (hasNext.value) {
      currentIndex.value++
    } else {
      stop()
    }
  }

  const prev = () => {
    if (hasPrev.value) {
      currentIndex.value--
    }
  }

  const goTo = (index: number) => {
    if (index >= 0 && index < photos.value.length) {
      currentIndex.value = index
    }
  }

  const play = () => {
    if (photos.value.length === 0) return
    isPlaying.value = true
    timer = setInterval(next, intervalMs.value)
  }

  const pause = () => {
    isPlaying.value = false
    if (timer) {
      clearInterval(timer)
      timer = null
    }
  }

  const stop = () => {
    pause()
    currentIndex.value = 0
  }

  const setSpeed = (ms: number) => {
    intervalMs.value = ms
    if (isPlaying.value) {
      pause()
      play()
    }
  }

  const setTransition = (type: 'fade' | 'slide') => {
    transition.value = type
  }

  onUnmounted(() => {
    if (timer) clearInterval(timer)
  })

  return {
    currentIndex,
    currentPhoto,
    totalCount,
    isPlaying,
    intervalMs,
    transition,
    hasNext,
    hasPrev,
    play,
    pause,
    stop,
    next,
    prev,
    goTo,
    setSpeed,
    setTransition,
  }
}

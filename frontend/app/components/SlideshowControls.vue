<script setup lang="ts">
const props = defineProps<{
  isPlaying: boolean
  currentIndex: number
  totalCount: number
  intervalMs: number
  transition: string
}>()

const emit = defineEmits<{
  play: []
  pause: []
  next: []
  prev: []
  'speed-change': [ms: number]
  'transition-change': [type: 'fade' | 'slide']
  close: []
}>()

const speedOptions = [
  { label: '2秒', value: 2000 },
  { label: '4秒', value: 4000 },
  { label: '6秒', value: 6000 },
]

const transitionOptions = [
  { label: 'フェード', value: 'fade' as const },
  { label: 'スライド', value: 'slide' as const },
]

const progressPercent = computed(() => {
  if (props.totalCount <= 1) return 100
  return ((props.currentIndex + 1) / props.totalCount) * 100
})
</script>

<template>
  <div class="absolute inset-0 z-10 flex flex-col justify-between">
    <!-- Top bar -->
    <div class="flex items-center justify-between bg-black/50 px-4 py-3">
      <span class="text-sm text-white/80">
        {{ currentIndex + 1 }} / {{ totalCount }}
      </span>
      <button
        class="flex h-10 w-10 items-center justify-center rounded-full bg-white/20 text-white transition-colors hover:bg-white/30"
        aria-label="閉じる"
        @click="emit('close')"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
        </svg>
      </button>
    </div>

    <!-- Bottom controls -->
    <div class="bg-black/50 px-4 pb-6 pt-4">
      <!-- Progress bar -->
      <div class="mb-4 h-1 w-full overflow-hidden rounded-full bg-white/20">
        <div
          class="h-full rounded-full bg-primary-500 transition-all duration-300"
          :style="{ width: `${progressPercent}%` }"
        />
      </div>

      <!-- Main controls -->
      <div class="mb-4 flex items-center justify-center gap-6">
        <!-- Prev -->
        <button
          class="flex h-12 w-12 items-center justify-center rounded-full bg-white/20 text-white transition-colors hover:bg-white/30 disabled:opacity-30"
          :disabled="currentIndex === 0"
          aria-label="前へ"
          @click="emit('prev')"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
          </svg>
        </button>

        <!-- Play / Pause -->
        <button
          class="flex h-14 w-14 items-center justify-center rounded-full bg-white/30 text-white transition-colors hover:bg-white/40"
          :aria-label="isPlaying ? '一時停止' : '再生'"
          @click="isPlaying ? emit('pause') : emit('play')"
        >
          <!-- Pause icon -->
          <svg v-if="isPlaying" xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zM7 8a1 1 0 012 0v4a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v4a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
          </svg>
          <!-- Play icon -->
          <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd" />
          </svg>
        </button>

        <!-- Next -->
        <button
          class="flex h-12 w-12 items-center justify-center rounded-full bg-white/20 text-white transition-colors hover:bg-white/30 disabled:opacity-30"
          :disabled="currentIndex >= totalCount - 1"
          aria-label="次へ"
          @click="emit('next')"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
          </svg>
        </button>
      </div>

      <!-- Settings row -->
      <div class="flex items-center justify-center gap-3">
        <!-- Speed buttons -->
        <div class="flex items-center gap-1 rounded-full bg-white/10 px-1 py-1">
          <button
            v-for="option in speedOptions"
            :key="option.value"
            class="rounded-full px-3 py-1 text-xs text-white transition-colors"
            :class="intervalMs === option.value ? 'bg-primary-500' : 'hover:bg-white/20'"
            @click="emit('speed-change', option.value)"
          >
            {{ option.label }}
          </button>
        </div>

        <!-- Transition buttons -->
        <div class="flex items-center gap-1 rounded-full bg-white/10 px-1 py-1">
          <button
            v-for="option in transitionOptions"
            :key="option.value"
            class="rounded-full px-3 py-1 text-xs text-white transition-colors"
            :class="transition === option.value ? 'bg-primary-500' : 'hover:bg-white/20'"
            @click="emit('transition-change', option.value)"
          >
            {{ option.label }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

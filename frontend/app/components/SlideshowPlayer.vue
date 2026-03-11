<script setup lang="ts">
import type { Photo } from '~/types/photo'

const props = defineProps<{
  photo: Photo | null
  transition: 'fade' | 'slide'
}>()

const previousPhoto = ref<Photo | null>(null)
const direction = ref<'next' | 'prev'>('next')
const isTransitioning = ref(false)

watch(() => props.photo, (newPhoto, oldPhoto) => {
  if (!oldPhoto || !newPhoto) return
  previousPhoto.value = oldPhoto

  const oldIndex = oldPhoto.id
  const newIndex = newPhoto.id
  direction.value = newIndex >= oldIndex ? 'next' : 'prev'

  isTransitioning.value = true
  setTimeout(() => {
    isTransitioning.value = false
    previousPhoto.value = null
  }, 500)
})

const transitionName = computed(() => {
  if (props.transition === 'slide') {
    return direction.value === 'next' ? 'slide-left' : 'slide-right'
  }
  return 'fade'
})
</script>

<template>
  <div class="relative h-full w-full overflow-hidden">
    <template v-if="photo">
      <Transition :name="transitionName" mode="out-in">
        <div :key="photo.id" class="absolute inset-0 flex items-center justify-center">
          <img
            :src="photo.storage_path"
            :alt="photo.caption ?? photo.original_filename"
            class="max-h-full max-w-full object-contain"
          >

          <!-- Caption overlay -->
          <div
            v-if="photo.caption"
            class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/70 to-transparent px-4 pb-6 pt-12"
          >
            <p class="text-center text-sm text-white/90">
              {{ photo.caption }}
            </p>
          </div>
        </div>
      </Transition>
    </template>

    <div v-else class="flex h-full items-center justify-center">
      <p class="text-lg text-white/60">
        写真がありません
      </p>
    </div>
  </div>
</template>

<style scoped>
/* Fade transition */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.5s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

/* Slide left (next) */
.slide-left-enter-active,
.slide-left-leave-active {
  transition: transform 0.5s ease;
}
.slide-left-enter-from {
  transform: translateX(100%);
}
.slide-left-leave-to {
  transform: translateX(-100%);
}

/* Slide right (prev) */
.slide-right-enter-active,
.slide-right-leave-active {
  transition: transform 0.5s ease;
}
.slide-right-enter-from {
  transform: translateX(-100%);
}
.slide-right-leave-to {
  transform: translateX(100%);
}
</style>

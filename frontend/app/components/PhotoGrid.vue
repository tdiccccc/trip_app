<script setup lang="ts">
import type { Photo } from '~/types/photo'

defineProps<{
  photos: Photo[]
}>()

const emit = defineEmits<{
  select: [photo: Photo]
}>()

const getImageUrl = (photo: Photo) => {
  return photo.thumbnail_path ?? photo.storage_path
}
</script>

<template>
  <div class="grid grid-cols-3 gap-1">
    <button
      v-for="photo in photos"
      :key="photo.id"
      class="relative aspect-square overflow-hidden rounded-lg bg-gray-100"
      @click="emit('select', photo)"
    >
      <img
        :src="getImageUrl(photo)"
        :alt="photo.caption ?? photo.original_filename"
        class="h-full w-full object-cover transition-transform hover:scale-105"
        loading="lazy"
      >
    </button>
  </div>

  <div
    v-if="photos.length === 0"
    class="py-12 text-center text-sm text-gray-400"
  >
    写真はまだありません
  </div>
</template>

<script setup lang="ts">
definePageMeta({
  layout: 'fullscreen',
  middleware: ['auth'],
})

useHead({
  title: 'スライドショー - Ise Trip',
})
</script>

<template>
  <div class="flex h-full items-center justify-center">
    <p class="text-white">
      スライドショー
    </p>
  </div>
</template>

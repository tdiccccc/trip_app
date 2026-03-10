<script setup lang="ts">
definePageMeta({
  middleware: ['auth'],
})

useHead({
  title: 'エクスポート - Ise Trip',
})
</script>

<template>
  <div>
    <h1 class="mb-4 text-xl font-bold text-gray-800">
      エクスポート
    </h1>
    <p class="text-gray-600">
      旅の記録をエクスポート
    </p>
  </div>
</template>

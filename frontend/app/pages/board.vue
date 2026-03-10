<script setup lang="ts">
definePageMeta({
  middleware: ['auth'],
})

useHead({
  title: 'ふたりの掲示板 - Ise Trip',
})
</script>

<template>
  <div>
    <h1 class="mb-4 text-xl font-bold text-gray-800">
      ふたりの掲示板
    </h1>
    <p class="text-gray-600">
      メッセージ一覧
    </p>
  </div>
</template>

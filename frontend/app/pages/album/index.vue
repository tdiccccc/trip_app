<script setup lang="ts">
definePageMeta({
  middleware: ['auth'],
})

useHead({
  title: 'アルバム - Ise Trip',
})
</script>

<template>
  <div>
    <h1 class="mb-4 text-xl font-bold text-gray-800">
      アルバム
    </h1>
    <p class="text-gray-600">
      写真一覧
    </p>
  </div>
</template>

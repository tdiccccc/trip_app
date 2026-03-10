<script setup lang="ts">
definePageMeta({
  middleware: ['auth'],
})

useHead({
  title: '費用メモ - Ise Trip',
})
</script>

<template>
  <div>
    <h1 class="mb-4 text-xl font-bold text-gray-800">
      費用メモ
    </h1>
    <p class="text-gray-600">
      支出一覧
    </p>
  </div>
</template>

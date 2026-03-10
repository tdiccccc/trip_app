<script setup lang="ts">
definePageMeta({
  middleware: ['auth'],
})

useHead({
  title: 'パッキングリスト - Ise Trip',
})
</script>

<template>
  <div>
    <h1 class="mb-4 text-xl font-bold text-gray-800">
      パッキングリスト
    </h1>
    <p class="text-gray-600">
      持ち物チェック
    </p>
  </div>
</template>

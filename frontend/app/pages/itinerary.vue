<script setup lang="ts">
definePageMeta({
  middleware: ['auth'],
})

useHead({
  title: 'しおり - Ise Trip',
})
</script>

<template>
  <div>
    <h1 class="mb-4 text-xl font-bold text-gray-800">
      しおり
    </h1>
    <p class="text-gray-600">
      タイムライン表示
    </p>
  </div>
</template>

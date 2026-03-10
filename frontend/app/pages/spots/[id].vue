<script setup lang="ts">
definePageMeta({
  middleware: ['auth'],
})

const route = useRoute()
const spotId = route.params.id

useHead({
  title: 'スポット詳細 - Ise Trip',
})
</script>

<template>
  <div>
    <h1 class="mb-4 text-xl font-bold text-gray-800">
      スポット詳細
    </h1>
    <p class="text-gray-600">
      スポット ID: {{ spotId }}
    </p>
  </div>
</template>

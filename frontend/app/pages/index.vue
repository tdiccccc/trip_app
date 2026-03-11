<script setup lang="ts">
definePageMeta({
  middleware: ['auth'],
})

// Redirect to trips list
navigateTo('/trips', { replace: true })
</script>

<template>
  <div />
</template>

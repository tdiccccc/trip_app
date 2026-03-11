<script setup lang="ts">
import type { SpotMemo } from '~/types/spot'

defineProps<{
  memo: SpotMemo
}>()

const formatDate = (dateStr: string) => {
  const date = new Date(dateStr)
  return date.toLocaleString('ja-JP', {
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  })
}
</script>

<template>
  <div class="rounded-xl bg-amber-50 p-3">
    <p class="whitespace-pre-wrap text-sm text-gray-700">
      {{ memo.body }}
    </p>
    <p class="mt-1.5 text-xs text-gray-400">
      {{ formatDate(memo.created_at) }}
    </p>
  </div>
</template>

<script setup lang="ts">
const props = defineProps<{
  transport: string | null
}>()

const iconMap: Record<string, string> = {
  train: '\uD83D\uDE83',
  car: '\uD83D\uDE97',
  walk: '\uD83D\uDEB6',
  bus: '\uD83D\uDE8C',
  none: '\u23F8',
}

const icon = computed(() => {
  if (!props.transport) return ''
  return iconMap[props.transport] ?? ''
})

const labelMap: Record<string, string> = {
  train: '電車',
  car: '車',
  walk: '徒歩',
  bus: 'バス',
  none: 'なし',
}

const label = computed(() => {
  if (!props.transport) return ''
  return labelMap[props.transport] ?? ''
})
</script>

<template>
  <span
    v-if="icon"
    class="inline-flex items-center gap-1 text-sm"
    :title="label"
  >
    <span class="text-base">{{ icon }}</span>
    <span class="text-xs text-gray-500">{{ label }}</span>
  </span>
</template>

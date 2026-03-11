<script setup lang="ts">
import type { CreateItineraryItemInput, TransportType } from '~/types/itinerary'

const props = defineProps<{
  initialData?: Partial<CreateItineraryItemInput>
  isEdit?: boolean
}>()

const emit = defineEmits<{
  submit: [data: CreateItineraryItemInput]
  cancel: []
}>()

const transportOptions: { value: TransportType; label: string }[] = [
  { value: 'train', label: '電車' },
  { value: 'car', label: '車' },
  { value: 'walk', label: '徒歩' },
  { value: 'bus', label: 'バス' },
  { value: 'taxi', label: 'タクシー' },
  { value: 'none', label: 'なし' },
]

const form = reactive<CreateItineraryItemInput>({
  title: props.initialData?.title ?? '',
  date: props.initialData?.date ?? '',
  start_time: props.initialData?.start_time ?? null,
  end_time: props.initialData?.end_time ?? null,
  memo: props.initialData?.memo ?? null,
  transport: props.initialData?.transport ?? null,
  spot_id: props.initialData?.spot_id ?? null,
  sort_order: props.initialData?.sort_order ?? null,
})

const isValid = computed(() => form.title.trim() !== '' && form.date !== '')

const handleSubmit = () => {
  if (!isValid.value) return
  emit('submit', { ...form })
}
</script>

<template>
  <form
    class="space-y-4 p-4"
    @submit.prevent="handleSubmit"
  >
    <!-- Title -->
    <div>
      <label class="mb-1 block text-sm font-medium text-gray-700">
        タイトル <span class="text-red-500">*</span>
      </label>
      <input
        v-model="form.title"
        type="text"
        placeholder="例: 伊勢神宮参拝"
        class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-base focus:border-primary-400 focus:outline-none focus:ring-2 focus:ring-primary-200"
      >
    </div>

    <!-- Date -->
    <div>
      <label class="mb-1 block text-sm font-medium text-gray-700">
        日付 <span class="text-red-500">*</span>
      </label>
      <input
        v-model="form.date"
        type="date"
        class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-base focus:border-primary-400 focus:outline-none focus:ring-2 focus:ring-primary-200"
      >
    </div>

    <!-- Time range -->
    <div class="flex gap-3">
      <div class="flex-1">
        <label class="mb-1 block text-sm font-medium text-gray-700">開始</label>
        <input
          v-model="form.start_time"
          type="time"
          class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-base focus:border-primary-400 focus:outline-none focus:ring-2 focus:ring-primary-200"
        >
      </div>
      <div class="flex-1">
        <label class="mb-1 block text-sm font-medium text-gray-700">終了</label>
        <input
          v-model="form.end_time"
          type="time"
          class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-base focus:border-primary-400 focus:outline-none focus:ring-2 focus:ring-primary-200"
        >
      </div>
    </div>

    <!-- Memo -->
    <div>
      <label class="mb-1 block text-sm font-medium text-gray-700">メモ</label>
      <textarea
        v-model="form.memo"
        rows="2"
        placeholder="メモを入力..."
        class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-base focus:border-primary-400 focus:outline-none focus:ring-2 focus:ring-primary-200"
      />
    </div>

    <!-- Transport -->
    <div>
      <label class="mb-1 block text-sm font-medium text-gray-700">交通手段</label>
      <div class="flex flex-wrap gap-2">
        <button
          v-for="opt in transportOptions"
          :key="opt.value"
          type="button"
          class="rounded-full border px-3 py-1.5 text-sm transition-colors"
          :class="form.transport === opt.value
            ? 'border-primary-400 bg-primary-50 text-primary-700'
            : 'border-gray-300 bg-white text-gray-600 hover:bg-gray-50'"
          @click="form.transport = form.transport === opt.value ? null : opt.value"
        >
          {{ opt.label }}
        </button>
      </div>
    </div>

    <!-- Buttons -->
    <div class="flex gap-3 pt-2">
      <button
        type="button"
        class="flex-1 rounded-xl border border-gray-300 px-4 py-2.5 text-base text-gray-600 hover:bg-gray-50"
        @click="emit('cancel')"
      >
        キャンセル
      </button>
      <button
        type="submit"
        :disabled="!isValid"
        class="flex-1 rounded-xl bg-primary-500 px-4 py-2.5 text-base font-semibold text-white transition-colors hover:bg-primary-600 disabled:cursor-not-allowed disabled:opacity-50"
      >
        {{ isEdit ? '更新' : '追加' }}
      </button>
    </div>
  </form>
</template>

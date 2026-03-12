<script setup lang="ts">
import type { Trip } from '~/types/trip'

const props = defineProps<{
  trip: Trip
}>()

const emit = defineEmits<{
  close: []
  updated: []
}>()

const { updateTrip } = useTrips()

const title = ref(props.trip.title)
const destination = ref(props.trip.destination ?? '')
const startDate = ref(props.trip.start_date)
const endDate = ref(props.trip.end_date)
const description = ref(props.trip.description ?? '')

const isSubmitting = ref(false)
const errorMessage = ref('')

const handleSubmit = async () => {
  if (!title.value.trim() || !startDate.value || !endDate.value) return
  if (isSubmitting.value) return

  isSubmitting.value = true
  errorMessage.value = ''

  try {
    await updateTrip(props.trip.id, {
      title: title.value.trim(),
      description: description.value.trim() || null,
      destination: destination.value.trim() || null,
      start_date: startDate.value,
      end_date: endDate.value,
    })
    emit('updated')
  } catch (error: unknown) {
    if (
      error !== null
      && typeof error === 'object'
      && 'data' in error
      && error.data !== null
      && typeof error.data === 'object'
      && 'message' in (error.data as Record<string, unknown>)
    ) {
      errorMessage.value = (error.data as { message: string }).message
    } else {
      errorMessage.value = '旅行の更新に失敗しました。'
    }
  } finally {
    isSubmitting.value = false
  }
}
</script>

<template>
  <Teleport to="body">
    <div
      class="fixed inset-0 z-50 flex items-end justify-center sm:items-center"
      @click.self="emit('close')"
    >
      <!-- Overlay -->
      <div
        class="fixed inset-0 bg-black/40"
        @click="emit('close')"
      />

      <!-- Modal -->
      <div class="relative z-10 w-full max-w-md rounded-t-2xl bg-white p-6 shadow-xl sm:rounded-2xl">
        <!-- Header -->
        <div class="mb-5 flex items-center justify-between">
          <h2 class="text-lg font-bold text-gray-800">
            旅行を編集
          </h2>
          <button
            class="flex h-8 w-8 items-center justify-center rounded-full text-gray-400 hover:bg-gray-100"
            @click="emit('close')"
          >
            <svg
              class="h-5 w-5"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M6 18L18 6M6 6l12 12"
              />
            </svg>
          </button>
        </div>

        <!-- Error message -->
        <div
          v-if="errorMessage"
          class="mb-4 rounded-xl bg-red-50 p-3 text-sm text-red-600"
          role="alert"
        >
          {{ errorMessage }}
        </div>

        <!-- Form -->
        <form
          class="space-y-4"
          @submit.prevent="handleSubmit"
        >
          <div>
            <label
              for="edit-title"
              class="mb-1 block text-sm font-medium text-gray-700"
            >
              タイトル <span class="text-red-400">*</span>
            </label>
            <input
              id="edit-title"
              v-model="title"
              type="text"
              required
              class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-400 focus:outline-none focus:ring-2 focus:ring-primary-200"
            >
          </div>

          <div>
            <label
              for="edit-destination"
              class="mb-1 block text-sm font-medium text-gray-700"
            >
              行き先
            </label>
            <input
              id="edit-destination"
              v-model="destination"
              type="text"
              class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-400 focus:outline-none focus:ring-2 focus:ring-primary-200"
            >
          </div>

          <div class="flex gap-3">
            <div class="flex-1">
              <label
                for="edit-start-date"
                class="mb-1 block text-sm font-medium text-gray-700"
              >
                開始日 <span class="text-red-400">*</span>
              </label>
              <input
                id="edit-start-date"
                v-model="startDate"
                type="date"
                required
                class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-400 focus:outline-none focus:ring-2 focus:ring-primary-200"
              >
            </div>
            <div class="flex-1">
              <label
                for="edit-end-date"
                class="mb-1 block text-sm font-medium text-gray-700"
              >
                終了日 <span class="text-red-400">*</span>
              </label>
              <input
                id="edit-end-date"
                v-model="endDate"
                type="date"
                required
                :min="startDate"
                class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-400 focus:outline-none focus:ring-2 focus:ring-primary-200"
              >
            </div>
          </div>

          <div>
            <label
              for="edit-description"
              class="mb-1 block text-sm font-medium text-gray-700"
            >
              説明
            </label>
            <textarea
              id="edit-description"
              v-model="description"
              rows="3"
              class="w-full resize-none rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-400 focus:outline-none focus:ring-2 focus:ring-primary-200"
            />
          </div>

          <button
            type="submit"
            :disabled="!title.trim() || !startDate || !endDate || isSubmitting"
            class="w-full rounded-xl bg-primary-500 py-3 text-sm font-semibold text-white transition-colors hover:bg-primary-600 disabled:cursor-not-allowed disabled:opacity-50"
          >
            <span v-if="isSubmitting">更新中...</span>
            <span v-else>保存する</span>
          </button>
        </form>
      </div>
    </div>
  </Teleport>
</template>

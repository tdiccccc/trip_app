<script setup lang="ts">
import type { Photo } from '~/types/photo'

const emit = defineEmits<{
  uploaded: [photo: Photo]
}>()

const props = defineProps<{
  spotId?: number
}>()

const { uploadPhoto } = useAlbum()

const fileInput = ref<HTMLInputElement | null>(null)
const caption = ref('')
const isUploading = ref(false)
const previewUrl = ref<string | null>(null)
const selectedFile = ref<File | null>(null)
const errorMessage = ref('')

const handleFileSelect = (event: Event) => {
  const target = event.target as HTMLInputElement
  const file = target.files?.[0]
  if (!file) return

  if (!file.type.startsWith('image/')) {
    errorMessage.value = 'Please select an image file'
    return
  }

  errorMessage.value = ''
  selectedFile.value = file
  previewUrl.value = URL.createObjectURL(file)
}

const handleUpload = async () => {
  if (!selectedFile.value) return

  isUploading.value = true
  errorMessage.value = ''

  try {
    const result = await uploadPhoto(selectedFile.value, {
      spot_id: props.spotId,
      caption: caption.value || undefined,
    })
    emit('uploaded', result.data)
    resetForm()
  } catch {
    errorMessage.value = 'Upload failed. Please try again.'
  } finally {
    isUploading.value = false
  }
}

const resetForm = () => {
  selectedFile.value = null
  caption.value = ''
  if (previewUrl.value) {
    URL.revokeObjectURL(previewUrl.value)
    previewUrl.value = null
  }
  if (fileInput.value) {
    fileInput.value.value = ''
  }
}

onUnmounted(() => {
  if (previewUrl.value) {
    URL.revokeObjectURL(previewUrl.value)
  }
})
</script>

<template>
  <div class="space-y-3">
    <!-- File select area -->
    <div
      v-if="!previewUrl"
      class="cursor-pointer rounded-2xl border-2 border-dashed border-gray-300 p-6 text-center transition-colors hover:border-primary-400"
      @click="fileInput?.click()"
    >
      <svg
        class="mx-auto h-10 w-10 text-gray-400"
        fill="none"
        stroke="currentColor"
        viewBox="0 0 24 24"
      >
        <path
          stroke-linecap="round"
          stroke-linejoin="round"
          stroke-width="1.5"
          d="M12 6v6m0 0v6m0-6h6m-6 0H6"
        />
      </svg>
      <p class="mt-2 text-sm text-gray-500">
        Tap to select a photo
      </p>
      <input
        ref="fileInput"
        type="file"
        accept="image/*"
        class="hidden"
        @change="handleFileSelect"
      >
    </div>

    <!-- Preview -->
    <div
      v-if="previewUrl"
      class="space-y-3"
    >
      <div class="relative overflow-hidden rounded-2xl">
        <img
          :src="previewUrl"
          alt="Preview"
          class="w-full rounded-2xl object-cover"
          style="max-height: 240px;"
        >
        <button
          class="absolute right-2 top-2 rounded-full bg-black/50 p-1.5 text-white"
          @click="resetForm"
        >
          <svg
            class="h-4 w-4"
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

      <!-- Caption -->
      <input
        v-model="caption"
        type="text"
        placeholder="Add a caption..."
        class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-400 focus:outline-none focus:ring-2 focus:ring-primary-200"
      >

      <!-- Upload button -->
      <button
        :disabled="isUploading"
        class="w-full rounded-xl bg-primary-500 px-4 py-2.5 text-base font-semibold text-white transition-colors hover:bg-primary-600 disabled:cursor-not-allowed disabled:opacity-50"
        @click="handleUpload"
      >
        <span v-if="isUploading">Uploading...</span>
        <span v-else>Upload</span>
      </button>
    </div>

    <!-- Error -->
    <p
      v-if="errorMessage"
      class="text-sm text-red-500"
    >
      {{ errorMessage }}
    </p>
  </div>
</template>

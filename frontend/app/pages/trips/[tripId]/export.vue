<script setup lang="ts">
definePageMeta({
  middleware: ['auth'],
})

useHead({
  title: 'エクスポート - Ise Trip',
})

const route = useRoute()
const tripId = route.params.tripId as string

const { apiFetch } = useApiClient()

interface ExportOption {
  key: string
  title: string
  description: string
  buttonLabel: string
  icon: string
  endpoint: string
  filename: string
  contentType: string
  disabled: boolean
}

const exportOptions: ExportOption[] = [
  {
    key: 'itinerary-pdf',
    title: 'PDFしおり',
    description: '旅行のしおりをPDFでダウンロード。行程やスポット情報がまとまった一冊に。',
    buttonLabel: 'PDFをダウンロード',
    icon: 'document',
    endpoint: `/api/trips/${tripId}/export/itinerary-pdf`,
    filename: 'itinerary.pdf',
    contentType: 'application/pdf',
    disabled: false,
  },
  {
    key: 'photobook-pdf',
    title: 'フォトブックPDF',
    description: '旅行の写真をまとめたフォトブックPDFをダウンロード。思い出を一冊に。',
    buttonLabel: 'フォトブックをダウンロード',
    icon: 'photo',
    endpoint: `/api/trips/${tripId}/export/photobook-pdf`,
    filename: 'photobook.pdf',
    contentType: 'application/pdf',
    disabled: false,
  },
  {
    key: 'slideshow-video',
    title: 'スライドショー動画',
    description: '写真をスライドショー動画に変換。BGM付きの思い出ムービーを作成。',
    buttonLabel: '準備中',
    icon: 'video',
    endpoint: '',
    filename: '',
    contentType: '',
    disabled: true,
  },
  {
    key: 'zip',
    title: 'ZIP一括ダウンロード',
    description: '写真・しおりデータをまとめてZIPファイルでダウンロード。バックアップにも。',
    buttonLabel: 'ZIPをダウンロード',
    icon: 'archive',
    endpoint: `/api/trips/${tripId}/export/zip`,
    filename: 'ise-trip-export.zip',
    contentType: 'application/zip',
    disabled: false,
  },
]

const downloadingKeys = ref<Set<string>>(new Set())
const errorMessage = ref('')

const isDownloading = (key: string) => downloadingKeys.value.has(key)

const handleDownload = async (option: ExportOption) => {
  if (option.disabled || isDownloading(option.key)) return

  downloadingKeys.value.add(option.key)
  errorMessage.value = ''

  try {
    const blob = await apiFetch<Blob>(option.endpoint, {
      method: 'POST',
      responseType: 'blob',
    })

    const url = URL.createObjectURL(blob)
    const link = document.createElement('a')
    link.href = url
    link.download = option.filename
    document.body.appendChild(link)
    link.click()
    document.body.removeChild(link)
    URL.revokeObjectURL(url)
  } catch {
    errorMessage.value = `${option.title}のダウンロードに失敗しました。もう一度お試しください。`
  } finally {
    downloadingKeys.value.delete(option.key)
  }
}
</script>

<template>
  <div>
    <!-- Header -->
    <div class="mb-4">
      <h1 class="text-xl font-bold text-gray-800">
        エクスポート
      </h1>
      <p class="mt-1 text-sm text-gray-500">
        旅の記録をダウンロード
      </p>
    </div>

    <!-- Error message -->
    <div
      v-if="errorMessage"
      class="mb-4 rounded-xl bg-red-50 px-4 py-3 text-sm text-red-600"
    >
      <div class="flex items-center justify-between">
        <span>{{ errorMessage }}</span>
        <button
          class="ml-2 text-red-400 hover:text-red-600"
          @click="errorMessage = ''"
        >
          <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>
    </div>

    <!-- Export cards -->
    <div class="space-y-3">
      <div
        v-for="option in exportOptions"
        :key="option.key"
        class="rounded-2xl bg-white p-4 shadow-sm transition-opacity"
        :class="option.disabled ? 'opacity-50' : ''"
      >
        <div class="flex items-start gap-3">
          <!-- Icon -->
          <div
            class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-xl"
            :class="option.disabled ? 'bg-gray-100' : 'bg-primary-50'"
          >
            <!-- Document icon -->
            <svg
              v-if="option.icon === 'document'"
              class="h-5 w-5"
              :class="option.disabled ? 'text-gray-400' : 'text-primary-500'"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <!-- Photo icon -->
            <svg
              v-if="option.icon === 'photo'"
              class="h-5 w-5"
              :class="option.disabled ? 'text-gray-400' : 'text-primary-500'"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            <!-- Video icon -->
            <svg
              v-if="option.icon === 'video'"
              class="h-5 w-5"
              :class="option.disabled ? 'text-gray-400' : 'text-primary-500'"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
            </svg>
            <!-- Archive icon -->
            <svg
              v-if="option.icon === 'archive'"
              class="h-5 w-5"
              :class="option.disabled ? 'text-gray-400' : 'text-primary-500'"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
            </svg>
          </div>

          <!-- Content -->
          <div class="min-w-0 flex-1">
            <div class="flex items-center gap-2">
              <h2 class="text-sm font-semibold text-gray-800">
                {{ option.title }}
              </h2>
              <span
                v-if="option.disabled"
                class="rounded-full bg-gray-100 px-2 py-0.5 text-xs text-gray-400"
              >
                Beta
              </span>
            </div>
            <p class="mt-0.5 text-xs leading-relaxed text-gray-500">
              {{ option.description }}
            </p>

            <!-- Download button -->
            <button
              class="mt-3 w-full rounded-xl py-2.5 text-sm font-semibold transition-colors"
              :class="option.disabled
                ? 'cursor-not-allowed bg-gray-100 text-gray-400'
                : isDownloading(option.key)
                  ? 'bg-primary-100 text-primary-400'
                  : 'bg-primary-500 text-white hover:bg-primary-600'"
              :disabled="option.disabled || isDownloading(option.key)"
              @click="handleDownload(option)"
            >
              <span v-if="isDownloading(option.key)" class="inline-flex items-center gap-2">
                <svg class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
                </svg>
                ダウンロード中...
              </span>
              <span v-else>{{ option.buttonLabel }}</span>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Note -->
    <p class="mt-4 text-center text-xs text-gray-400">
      ファイルサイズが大きい場合、ダウンロードに時間がかかることがあります
    </p>
  </div>
</template>

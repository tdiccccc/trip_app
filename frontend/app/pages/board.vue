<script setup lang="ts">
import type { BoardPost } from '~/types/board'

definePageMeta({
  middleware: ['auth'],
})

useHead({
  title: 'ふたりの掲示板 - Ise Trip',
})

const { fetchPosts, createPost, addReaction } = useBoard()
const { user } = useAuth()

// Fetch posts
const { data: response, refresh } = fetchPosts()

const posts = computed<BoardPost[]>(() => {
  if (!response.value?.data) return []
  return [...response.value.data].sort(
    (a, b) => new Date(b.created_at).getTime() - new Date(a.created_at).getTime(),
  )
})

// New post form
const newBody = ref('')
const isSubmitting = ref(false)

const handleSubmitPost = async () => {
  const body = newBody.value.trim()
  if (!body || isSubmitting.value) return

  isSubmitting.value = true
  try {
    await createPost({ body })
    newBody.value = ''
    await refresh()
  } catch {
    // Error handling
  } finally {
    isSubmitting.value = false
  }
}

// Reactions
const EMOJI_OPTIONS = ['\u{1F60A}', '\u{1F44D}', '\u2764\uFE0F', '\u{1F389}', '\u2728', '\u{1F602}']
const showEmojiPicker = ref<number | null>(null)

const toggleEmojiPicker = (postId: number) => {
  showEmojiPicker.value = showEmojiPicker.value === postId ? null : postId
}

const handleAddReaction = async (postId: number, emoji: string) => {
  showEmojiPicker.value = null
  try {
    await addReaction(postId, { emoji })
    await refresh()
  } catch {
    // Error handling
  }
}

// Group reactions by emoji
const groupedReactions = (post: BoardPost) => {
  const groups: Record<string, { count: number; userIds: number[] }> = {}
  for (const r of post.reactions) {
    if (!groups[r.emoji]) {
      groups[r.emoji] = { count: 0, userIds: [] }
    }
    groups[r.emoji].count++
    groups[r.emoji].userIds.push(r.user_id)
  }
  return groups
}

// Format date
const formatDate = (dateStr: string) => {
  const d = new Date(dateStr)
  const month = d.getMonth() + 1
  const day = d.getDate()
  const hours = String(d.getHours()).padStart(2, '0')
  const minutes = String(d.getMinutes()).padStart(2, '0')
  return `${month}/${day} ${hours}:${minutes}`
}

// Check if current user's post
const isOwnPost = (post: BoardPost) => {
  return user.value?.id === post.user_id
}
</script>

<template>
  <div class="flex h-full flex-col">
    <!-- Header -->
    <div class="mb-4">
      <h1 class="text-xl font-bold text-gray-800">
        ふたりの掲示板
      </h1>
    </div>

    <!-- Posts list -->
    <div class="flex-1 space-y-3 overflow-y-auto pb-24">
      <div
        v-if="posts.length === 0"
        class="py-16 text-center"
      >
        <p class="text-sm text-gray-400">
          まだ投稿がありません
        </p>
      </div>

      <div
        v-for="post in posts"
        :key="post.id"
        class="rounded-2xl bg-white p-4 shadow-sm"
        :class="isOwnPost(post) ? 'ml-8' : 'mr-8'"
      >
        <!-- User & date -->
        <div class="mb-2 flex items-center justify-between">
          <span class="text-sm font-semibold text-primary-700">
            {{ post.user_name }}
          </span>
          <span class="text-xs text-gray-400">
            {{ formatDate(post.created_at) }}
          </span>
        </div>

        <!-- Body -->
        <p class="whitespace-pre-wrap text-sm leading-relaxed text-gray-700">
          {{ post.body }}
        </p>

        <!-- Reactions -->
        <div class="mt-3 flex flex-wrap items-center gap-1.5">
          <button
            v-for="(info, emoji) in groupedReactions(post)"
            :key="emoji"
            class="inline-flex items-center gap-1 rounded-full border px-2 py-0.5 text-xs transition-colors"
            :class="info.userIds.includes(user?.id ?? -1)
              ? 'border-primary-300 bg-primary-50'
              : 'border-gray-200 bg-gray-50 hover:bg-gray-100'"
            @click="handleAddReaction(post.id, emoji as string)"
          >
            <span>{{ emoji }}</span>
            <span class="text-gray-500">{{ info.count }}</span>
          </button>

          <!-- Add reaction button -->
          <div class="relative">
            <button
              class="inline-flex h-6 w-6 items-center justify-center rounded-full border border-gray-200 text-xs text-gray-400 transition-colors hover:bg-gray-50"
              @click="toggleEmojiPicker(post.id)"
            >
              +
            </button>

            <!-- Emoji picker -->
            <div
              v-if="showEmojiPicker === post.id"
              class="absolute bottom-8 left-0 z-10 flex gap-1 rounded-xl border border-gray-200 bg-white p-2 shadow-lg"
            >
              <button
                v-for="emoji in EMOJI_OPTIONS"
                :key="emoji"
                class="rounded-lg p-1.5 text-lg transition-colors hover:bg-primary-50"
                @click="handleAddReaction(post.id, emoji)"
              >
                {{ emoji }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- New post form (fixed bottom) -->
    <div class="fixed bottom-16 left-0 right-0 z-20 border-t border-gray-100 bg-white px-4 py-3 pb-safe">
      <form
        class="flex items-end gap-2"
        @submit.prevent="handleSubmitPost"
      >
        <textarea
          v-model="newBody"
          class="flex-1 resize-none rounded-xl border border-gray-300 px-3 py-2 text-sm focus:border-primary-400 focus:outline-none focus:ring-2 focus:ring-primary-200"
          rows="1"
          placeholder="メッセージを入力..."
          @input="($event.target as HTMLTextAreaElement).style.height = 'auto'; ($event.target as HTMLTextAreaElement).style.height = ($event.target as HTMLTextAreaElement).scrollHeight + 'px'"
        />
        <button
          type="submit"
          :disabled="!newBody.trim() || isSubmitting"
          class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-primary-500 text-white transition-colors hover:bg-primary-600 disabled:bg-gray-300"
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
              d="M12 19V5m0 0l-7 7m7-7l7 7"
            />
          </svg>
        </button>
      </form>
    </div>
  </div>
</template>

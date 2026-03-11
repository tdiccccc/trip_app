<script setup lang="ts">
import type { PackingItem } from '~/types/packing'

definePageMeta({
  middleware: ['auth'],
})

useHead({
  title: 'パッキングリスト - Ise Trip',
})

const { fetchItems, createItem, updateItem, deleteItem } = usePacking()

// Filter tabs
type AssigneeFilter = 'all' | 'taro' | 'hanako'
const FILTER_TABS: { key: AssigneeFilter; label: string }[] = [
  { key: 'all', label: '全て' },
  { key: 'taro', label: 'たろう' },
  { key: 'hanako', label: 'はなこ' },
]
const selectedFilter = ref<AssigneeFilter>('all')

// Fetch items
const { data: response, refresh } = fetchItems()

const items = computed<PackingItem[]>(() => {
  if (!response.value?.data) return []
  const all = response.value.data
  if (selectedFilter.value === 'all') return all
  return all.filter((item) => item.assignee === selectedFilter.value)
})

// Progress
const checkedCount = computed(() => items.value.filter((i) => i.is_checked).length)
const totalCount = computed(() => items.value.length)
const progressPercent = computed(() => {
  if (totalCount.value === 0) return 0
  return Math.round((checkedCount.value / totalCount.value) * 100)
})

// Toggle check
const handleToggle = async (item: PackingItem) => {
  try {
    await updateItem(item.id, { is_checked: !item.is_checked })
    await refresh()
  } catch {
    // Error handling
  }
}

// Delete
const handleDelete = async (id: number) => {
  if (!confirm('このアイテムを削除しますか？')) return
  try {
    await deleteItem(id)
    await refresh()
  } catch {
    // Error handling
  }
}

// Add form
const showAddForm = ref(false)
const newName = ref('')
const newAssignee = ref<string>('')
const isSubmitting = ref(false)

const handleAdd = async () => {
  const name = newName.value.trim()
  if (!name || isSubmitting.value) return

  isSubmitting.value = true
  try {
    await createItem({
      name,
      assignee: newAssignee.value || undefined,
    })
    newName.value = ''
    newAssignee.value = ''
    await refresh()
  } catch {
    // Error handling
  } finally {
    isSubmitting.value = false
  }
}

// Assignee display
const assigneeLabel = (assignee: string | null) => {
  if (assignee === 'taro') return 'たろう'
  if (assignee === 'hanako') return 'はなこ'
  return ''
}
</script>

<template>
  <div>
    <!-- Header -->
    <div class="mb-4 flex items-center justify-between">
      <h1 class="text-xl font-bold text-gray-800">
        パッキングリスト
      </h1>
      <button
        class="rounded-xl bg-primary-500 px-4 py-2 text-sm font-semibold text-white transition-colors hover:bg-primary-600"
        @click="showAddForm = !showAddForm"
      >
        {{ showAddForm ? '閉じる' : '追加' }}
      </button>
    </div>

    <!-- Progress bar -->
    <div class="mb-4 rounded-xl bg-white p-3 shadow-sm">
      <div class="mb-1 flex items-center justify-between text-sm">
        <span class="text-gray-600">準備状況</span>
        <span class="font-semibold text-primary-700">
          {{ checkedCount }} / {{ totalCount }}
        </span>
      </div>
      <div class="h-2.5 overflow-hidden rounded-full bg-gray-100">
        <div
          class="h-full rounded-full bg-primary-500 transition-all duration-300"
          :style="{ width: `${progressPercent}%` }"
        />
      </div>
    </div>

    <!-- Add form -->
    <div
      v-if="showAddForm"
      class="mb-4 rounded-xl bg-white p-4 shadow-sm"
    >
      <form
        class="space-y-3"
        @submit.prevent="handleAdd"
      >
        <input
          v-model="newName"
          type="text"
          placeholder="アイテム名"
          class="w-full rounded-xl border border-gray-300 px-3 py-2.5 text-sm focus:border-primary-400 focus:outline-none focus:ring-2 focus:ring-primary-200"
        >
        <div class="flex gap-2">
          <select
            v-model="newAssignee"
            class="flex-1 rounded-xl border border-gray-300 bg-white px-3 py-2.5 text-sm focus:border-primary-400 focus:outline-none focus:ring-2 focus:ring-primary-200"
          >
            <option value="">
              担当者なし
            </option>
            <option value="taro">
              たろう
            </option>
            <option value="hanako">
              はなこ
            </option>
          </select>
          <button
            type="submit"
            :disabled="!newName.trim() || isSubmitting"
            class="rounded-xl bg-primary-500 px-6 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-primary-600 disabled:bg-gray-300"
          >
            追加
          </button>
        </div>
      </form>
    </div>

    <!-- Filter tabs -->
    <div class="mb-4 flex gap-2">
      <button
        v-for="tab in FILTER_TABS"
        :key="tab.key"
        class="rounded-full px-4 py-2 text-sm font-medium transition-colors"
        :class="selectedFilter === tab.key
          ? 'bg-primary-500 text-white shadow-sm'
          : 'bg-white text-gray-600 hover:bg-primary-50'"
        @click="selectedFilter = tab.key"
      >
        {{ tab.label }}
      </button>
    </div>

    <!-- Item list -->
    <div class="space-y-2">
      <div
        v-if="items.length === 0"
        class="py-16 text-center"
      >
        <p class="text-sm text-gray-400">
          アイテムがありません
        </p>
      </div>

      <div
        v-for="item in items"
        :key="item.id"
        class="flex items-center gap-3 rounded-xl bg-white px-4 py-3 shadow-sm transition-opacity"
        :class="item.is_checked ? 'opacity-50' : ''"
      >
        <!-- Checkbox -->
        <button
          class="flex h-6 w-6 flex-shrink-0 items-center justify-center rounded-lg border-2 transition-colors"
          :class="item.is_checked
            ? 'border-primary-500 bg-primary-500'
            : 'border-gray-300 hover:border-primary-400'"
          @click="handleToggle(item)"
        >
          <svg
            v-if="item.is_checked"
            class="h-4 w-4 text-white"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2.5"
              d="M5 13l4 4L19 7"
            />
          </svg>
        </button>

        <!-- Item info -->
        <div class="min-w-0 flex-1">
          <p
            class="text-sm"
            :class="item.is_checked ? 'text-gray-400 line-through' : 'text-gray-800'"
          >
            {{ item.name }}
          </p>
          <p
            v-if="item.assignee"
            class="text-xs text-gray-400"
          >
            {{ assigneeLabel(item.assignee) }}
          </p>
        </div>

        <!-- Delete button -->
        <button
          class="flex-shrink-0 rounded-lg p-1.5 text-gray-300 transition-colors hover:bg-red-50 hover:text-red-400"
          @click="handleDelete(item.id)"
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
              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
            />
          </svg>
        </button>
      </div>
    </div>
  </div>
</template>

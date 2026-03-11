<script setup lang="ts">
import type { Expense, CreateExpenseInput, ExpenseSummary } from '~/types/expense'

definePageMeta({
  middleware: ['auth'],
})

useHead({
  title: '費用メモ - Ise Trip',
})

const { fetchExpenses, fetchSummary, createExpense, deleteExpense } = useExpenses()

// Category definitions
const CATEGORIES: { key: string; label: string }[] = [
  { key: 'food', label: '食事' },
  { key: 'transport', label: '交通' },
  { key: 'ticket', label: 'チケット' },
  { key: 'souvenir', label: 'お土産' },
  { key: 'hotel', label: '宿泊' },
  { key: 'other', label: 'その他' },
]

const categoryLabel = (key: string) => {
  return CATEGORIES.find((c) => c.key === key)?.label ?? key
}

// Filter
const selectedCategory = ref<string>('')

// Fetch data
const { data: expensesResponse, refresh: refreshExpenses } = fetchExpenses()
const { data: summaryResponse, refresh: refreshSummary } = fetchSummary()

const expenses = computed<Expense[]>(() => {
  if (!expensesResponse.value?.data) return []
  const all = expensesResponse.value.data
  if (!selectedCategory.value) return all
  return all.filter((e) => e.category === selectedCategory.value)
})

const summary = computed<ExpenseSummary | null>(() => {
  return summaryResponse.value?.data ?? null
})

// Watch filter and refetch
watch(selectedCategory, async () => {
  const query = selectedCategory.value ? `?category=${selectedCategory.value}` : ''
  const result = useApiFetch<{ data: Expense[] }>(`/api/expenses${query}`)
  const { data: newData } = await result
  expensesResponse.value = newData.value
})

// Refresh both
const refreshAll = async () => {
  await Promise.all([refreshExpenses(), refreshSummary()])
}

// Delete
const handleDelete = async (id: number) => {
  if (!confirm('この費用を削除しますか？')) return
  try {
    await deleteExpense(id)
    await refreshAll()
  } catch {
    // Error handling
  }
}

// Add form
const showForm = ref(false)
const isSubmitting = ref(false)
const formData = reactive<CreateExpenseInput>({
  label: '',
  amount: 0,
  category: 'food',
  paid_by: 'taro',
  note: '',
})

const resetForm = () => {
  formData.label = ''
  formData.amount = 0
  formData.category = 'food'
  formData.paid_by = 'taro'
  formData.note = ''
}

const handleSubmit = async () => {
  if (!formData.label.trim() || formData.amount <= 0 || isSubmitting.value) return

  isSubmitting.value = true
  try {
    await createExpense({
      ...formData,
      note: formData.note || undefined,
    })
    resetForm()
    showForm.value = false
    await refreshAll()
  } catch {
    // Error handling
  } finally {
    isSubmitting.value = false
  }
}

// Format helpers
const formatAmount = (amount: number) => {
  return `\u00A5${amount.toLocaleString()}`
}

const formatDate = (dateStr: string) => {
  const d = new Date(dateStr)
  const month = d.getMonth() + 1
  const day = d.getDate()
  const hours = String(d.getHours()).padStart(2, '0')
  const minutes = String(d.getMinutes()).padStart(2, '0')
  return `${month}/${day} ${hours}:${minutes}`
}

const paidByLabel = (paidBy: string) => {
  if (paidBy === 'taro') return 'たろう'
  if (paidBy === 'hanako') return 'はなこ'
  return paidBy
}
</script>

<template>
  <div>
    <!-- Header -->
    <div class="mb-4 flex items-center justify-between">
      <h1 class="text-xl font-bold text-gray-800">
        費用メモ
      </h1>
      <button
        class="rounded-xl bg-primary-500 px-4 py-2 text-sm font-semibold text-white transition-colors hover:bg-primary-600"
        @click="showForm = !showForm"
      >
        {{ showForm ? '閉じる' : '追加' }}
      </button>
    </div>

    <!-- Summary card -->
    <div
      v-if="summary"
      class="mb-4 rounded-2xl bg-gradient-to-br from-primary-500 to-primary-600 p-4 text-white shadow-sm"
    >
      <div class="mb-3 flex items-center justify-between">
        <div>
          <p class="text-xs text-primary-100">
            合計金額
          </p>
          <p class="text-2xl font-bold">
            {{ formatAmount(summary.total) }}
          </p>
        </div>
        <div class="text-right">
          <p class="text-xs text-primary-100">
            一人あたり
          </p>
          <p class="text-lg font-semibold">
            {{ formatAmount(summary.per_person) }}
          </p>
        </div>
      </div>

      <!-- Category breakdown -->
      <div class="grid grid-cols-3 gap-2">
        <div
          v-for="(amount, catKey) in summary.by_category"
          :key="catKey"
          class="rounded-xl bg-white/15 px-2 py-1.5 text-center"
        >
          <p class="text-xs text-primary-100">
            {{ categoryLabel(catKey as string) }}
          </p>
          <p class="text-sm font-semibold">
            {{ formatAmount(amount) }}
          </p>
        </div>
      </div>
    </div>

    <!-- Add form -->
    <div
      v-if="showForm"
      class="mb-4 rounded-xl bg-white p-4 shadow-sm"
    >
      <form
        class="space-y-3"
        @submit.prevent="handleSubmit"
      >
        <input
          v-model="formData.label"
          type="text"
          placeholder="項目名"
          class="w-full rounded-xl border border-gray-300 px-3 py-2.5 text-sm focus:border-primary-400 focus:outline-none focus:ring-2 focus:ring-primary-200"
        >
        <input
          v-model.number="formData.amount"
          type="number"
          placeholder="金額"
          min="0"
          class="w-full rounded-xl border border-gray-300 px-3 py-2.5 text-sm focus:border-primary-400 focus:outline-none focus:ring-2 focus:ring-primary-200"
        >
        <div class="flex gap-2">
          <select
            v-model="formData.category"
            class="flex-1 rounded-xl border border-gray-300 bg-white px-3 py-2.5 text-sm focus:border-primary-400 focus:outline-none focus:ring-2 focus:ring-primary-200"
          >
            <option
              v-for="cat in CATEGORIES"
              :key="cat.key"
              :value="cat.key"
            >
              {{ cat.label }}
            </option>
          </select>
          <select
            v-model="formData.paid_by"
            class="flex-1 rounded-xl border border-gray-300 bg-white px-3 py-2.5 text-sm focus:border-primary-400 focus:outline-none focus:ring-2 focus:ring-primary-200"
          >
            <option value="taro">
              たろう
            </option>
            <option value="hanako">
              はなこ
            </option>
          </select>
        </div>
        <input
          v-model="formData.note"
          type="text"
          placeholder="メモ（任意）"
          class="w-full rounded-xl border border-gray-300 px-3 py-2.5 text-sm focus:border-primary-400 focus:outline-none focus:ring-2 focus:ring-primary-200"
        >
        <button
          type="submit"
          :disabled="!formData.label.trim() || formData.amount <= 0 || isSubmitting"
          class="w-full rounded-xl bg-primary-500 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-primary-600 disabled:bg-gray-300"
        >
          追加する
        </button>
      </form>
    </div>

    <!-- Category filter -->
    <div class="mb-4 flex gap-2 overflow-x-auto">
      <button
        class="flex-shrink-0 rounded-full px-4 py-2 text-sm font-medium transition-colors"
        :class="!selectedCategory
          ? 'bg-primary-500 text-white shadow-sm'
          : 'bg-white text-gray-600 hover:bg-primary-50'"
        @click="selectedCategory = ''"
      >
        全て
      </button>
      <button
        v-for="cat in CATEGORIES"
        :key="cat.key"
        class="flex-shrink-0 rounded-full px-4 py-2 text-sm font-medium transition-colors"
        :class="selectedCategory === cat.key
          ? 'bg-primary-500 text-white shadow-sm'
          : 'bg-white text-gray-600 hover:bg-primary-50'"
        @click="selectedCategory = cat.key"
      >
        {{ cat.label }}
      </button>
    </div>

    <!-- Expenses list -->
    <div class="space-y-2">
      <div
        v-if="expenses.length === 0"
        class="py-16 text-center"
      >
        <p class="text-sm text-gray-400">
          費用データがありません
        </p>
      </div>

      <div
        v-for="expense in expenses"
        :key="expense.id"
        class="rounded-xl bg-white p-4 shadow-sm"
      >
        <div class="flex items-start justify-between">
          <div class="min-w-0 flex-1">
            <div class="flex items-center gap-2">
              <p class="text-sm font-semibold text-gray-800">
                {{ expense.label }}
              </p>
              <span class="rounded-full bg-primary-50 px-2 py-0.5 text-xs text-primary-700">
                {{ categoryLabel(expense.category) }}
              </span>
            </div>
            <div class="mt-1 flex items-center gap-2 text-xs text-gray-400">
              <span>{{ paidByLabel(expense.paid_by) }}</span>
              <span>{{ formatDate(expense.created_at) }}</span>
            </div>
            <p
              v-if="expense.note"
              class="mt-1 text-xs text-gray-400"
            >
              {{ expense.note }}
            </p>
          </div>
          <div class="flex items-center gap-2">
            <p class="text-base font-bold text-gray-800">
              {{ formatAmount(expense.amount) }}
            </p>
            <button
              class="rounded-lg p-1.5 text-gray-300 transition-colors hover:bg-red-50 hover:text-red-400"
              @click="handleDelete(expense.id)"
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
    </div>
  </div>
</template>

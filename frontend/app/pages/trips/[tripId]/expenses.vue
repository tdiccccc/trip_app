<script setup lang="ts">
import type { Expense, CreateExpenseInput, ExpenseSummary } from '~/types/expense'

definePageMeta({
  middleware: ['auth'],
})

useHead({
  title: '費用メモ - Ise Trip',
})

const route = useRoute()
const tripId = route.params.tripId as string

const { fetchExpenses, fetchSummary, createExpense, deleteExpense } = useExpenses(tripId)
const { fetchTrip } = useTrips()
const { user } = useAuth()
const { fetchExpenseCategories, FALLBACK_EXPENSE_CATEGORIES } = useMaster()

// Fetch trip members
const { data: tripResponse } = fetchTrip(tripId)
const members = computed(() => tripResponse.value?.data?.members ?? [])

// Fetch expense categories from master API
const { data: categoriesResponse } = fetchExpenseCategories()
const categories = computed(() => categoriesResponse.value?.data ?? FALLBACK_EXPENSE_CATEGORIES)

const categoryLabel = (key: string) => {
  return categories.value.find((c) => c.key === key)?.label ?? key
}

// Paid by label - resolve from trip members
const paidByLabel = (userId: number) => {
  return members.value.find((m) => m.id === userId)?.name ?? '不明'
}

// Filter
const selectedCategory = ref<string>('')

// Fetch data (reactively watches selectedCategory)
const { data: expensesResponse, refresh: refreshExpenses } = fetchExpenses(selectedCategory)
const { data: summaryResponse, refresh: refreshSummary } = fetchSummary()

const expenses = computed<Expense[]>(() => {
  return expensesResponse.value?.data ?? []
})

const summary = computed<ExpenseSummary | null>(() => {
  return summaryResponse.value?.data ?? null
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

// Today's date in YYYY-MM-DD format
const todayStr = () => {
  const d = new Date()
  const y = d.getFullYear()
  const m = String(d.getMonth() + 1).padStart(2, '0')
  const day = String(d.getDate()).padStart(2, '0')
  return `${y}-${m}-${day}`
}

// Add form
const showForm = ref(false)
const isSubmitting = ref(false)
const formData = reactive<CreateExpenseInput>({
  description: '',
  amount: 0,
  category: 'food',
  paid_at: todayStr(),
  is_shared: true,
})

const resetForm = () => {
  formData.description = ''
  formData.amount = 0
  formData.category = 'food'
  formData.paid_at = todayStr()
  formData.is_shared = true
}

const handleSubmit = async () => {
  if (!formData.description.trim() || formData.amount <= 0 || isSubmitting.value) return

  isSubmitting.value = true
  try {
    await createExpense({
      description: formData.description,
      amount: formData.amount,
      category: formData.category,
      paid_at: formData.paid_at,
      is_shared: formData.is_shared,
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
  return `${month}/${day}`
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
          v-model="formData.description"
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
              v-for="cat in categories"
              :key="cat.key"
              :value="cat.key"
            >
              {{ cat.label }}
            </option>
          </select>
          <input
            v-model="formData.paid_at"
            type="date"
            class="flex-1 rounded-xl border border-gray-300 bg-white px-3 py-2.5 text-sm focus:border-primary-400 focus:outline-none focus:ring-2 focus:ring-primary-200"
          >
        </div>
        <label class="flex items-center gap-2 text-sm text-gray-600">
          <input
            v-model="formData.is_shared"
            type="checkbox"
            class="h-4 w-4 rounded border-gray-300 text-primary-500 focus:ring-primary-400"
          >
          割り勘にする
        </label>
        <button
          type="submit"
          :disabled="!formData.description.trim() || formData.amount <= 0 || isSubmitting"
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
        v-for="cat in categories"
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
                {{ expense.description }}
              </p>
              <span class="rounded-full bg-primary-50 px-2 py-0.5 text-xs text-primary-700">
                {{ categoryLabel(expense.category) }}
              </span>
            </div>
            <div class="mt-1 flex items-center gap-2 text-xs text-gray-400">
              <span>{{ paidByLabel(expense.user_id) }}</span>
              <span>{{ formatDate(expense.paid_at) }}</span>
              <span
                v-if="expense.is_shared"
                class="rounded-full bg-gray-100 px-1.5 py-0.5 text-xs text-gray-500"
              >
                割り勘
              </span>
            </div>
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

<script setup lang="ts">
import type { Expense, CreateExpenseInput, ExpenseSummary, ExpenseCategory } from '~/types/expense'

definePageMeta({
  middleware: ['auth'],
})

useHead({
  title: '費用メモ - Ise Trip',
})

const route = useRoute()
const tripId = route.params.tripId as string

const { fetchExpenses, fetchSummary, createExpense, deleteExpense } = useExpenses(tripId)
const {
  fetchCategories,
  createCategory,
  updateCategory,
  deleteCategory: deleteCategoryApi,
} = useExpenseCategories(tripId)
const { fetchTrip } = useTrips()

// Fetch trip members
const { data: tripResponse } = fetchTrip(tripId)
const members = computed(() => tripResponse.value?.data?.members ?? [])

// Fetch expense categories from trip-specific API
const { data: categoriesResponse, refresh: refreshCategories } = fetchCategories()
const categories = computed<ExpenseCategory[]>(() => categoriesResponse.value?.data ?? [])

const categoryLabel = (categoryObj: ExpenseCategory | undefined) => {
  return categoryObj?.name ?? '不明'
}

// Paid by label - resolve from trip members
const paidByLabel = (userId: number) => {
  return members.value.find((m) => m.id === userId)?.name ?? '不明'
}

// Filter
const selectedCategoryId = ref<number | null>(null)

// Fetch data (reactively watches selectedCategoryId)
const { data: expensesResponse, refresh: refreshExpenses } = fetchExpenses(selectedCategoryId)
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

// Delete expense
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

const defaultCategoryId = computed(() => categories.value[0]?.id ?? 0)

const formData = reactive<CreateExpenseInput>({
  description: '',
  amount: 0,
  expense_category_id: 0,
  paid_at: todayStr(),
  is_shared: true,
})

// Set default category when categories load
watch(defaultCategoryId, (id) => {
  if (formData.expense_category_id === 0 && id > 0) {
    formData.expense_category_id = id
  }
}, { immediate: true })

const resetForm = () => {
  formData.description = ''
  formData.amount = 0
  formData.expense_category_id = defaultCategoryId.value
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
      expense_category_id: formData.expense_category_id,
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

// Category management
const showCategoryManager = ref(false)
const newCategoryName = ref('')
const newCategoryColor = ref('#6366f1')
const isCreatingCategory = ref(false)
const categoryError = ref('')

// Edit state
const editingCategoryId = ref<number | null>(null)
const editingCategoryName = ref('')
const editingCategoryColor = ref('')

const handleCreateCategory = async () => {
  if (!newCategoryName.value.trim() || isCreatingCategory.value) return

  isCreatingCategory.value = true
  categoryError.value = ''
  try {
    await createCategory({
      name: newCategoryName.value.trim(),
      color: newCategoryColor.value,
    })
    newCategoryName.value = ''
    newCategoryColor.value = '#6366f1'
    await refreshCategories()
  } catch {
    categoryError.value = 'カテゴリの作成に失敗しました'
  } finally {
    isCreatingCategory.value = false
  }
}

const startEditCategory = (cat: ExpenseCategory) => {
  editingCategoryId.value = cat.id
  editingCategoryName.value = cat.name
  editingCategoryColor.value = cat.color ?? '#6366f1'
}

const cancelEditCategory = () => {
  editingCategoryId.value = null
  editingCategoryName.value = ''
  editingCategoryColor.value = ''
}

const handleUpdateCategory = async (id: number) => {
  if (!editingCategoryName.value.trim()) return

  categoryError.value = ''
  try {
    await updateCategory(id, {
      name: editingCategoryName.value.trim(),
      color: editingCategoryColor.value,
    })
    cancelEditCategory()
    await Promise.all([refreshCategories(), refreshAll()])
  } catch {
    categoryError.value = 'カテゴリの更新に失敗しました'
  }
}

const handleDeleteCategory = async (id: number) => {
  if (!confirm('このカテゴリを削除しますか？')) return

  categoryError.value = ''
  try {
    await deleteCategoryApi(id)
    await Promise.all([refreshCategories(), refreshAll()])
  } catch (err: unknown) {
    const fetchErr = err as { status?: number }
    if (fetchErr.status === 409) {
      categoryError.value = 'このカテゴリは費用で使用中のため削除できません'
    } else {
      categoryError.value = 'カテゴリの削除に失敗しました'
    }
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

// Summary category label resolver
const summaryCategories = computed(() => {
  if (!summary.value) return []
  return Object.entries(summary.value.by_category).map(([key, amount]) => {
    const cat = categories.value.find((c) => c.key === key || c.name === key)
    return { label: cat?.name ?? key, amount }
  })
})
</script>

<template>
  <div>
    <!-- Header -->
    <div class="mb-4 flex items-center justify-between">
      <h1 class="text-xl font-bold text-gray-800">
        費用メモ
      </h1>
      <div class="flex gap-2">
        <button
          class="rounded-xl px-3 py-2 text-sm font-medium transition-colors"
          :class="showCategoryManager
            ? 'bg-gray-200 text-gray-700'
            : 'bg-white text-gray-600 hover:bg-gray-100'"
          @click="showCategoryManager = !showCategoryManager"
        >
          カテゴリ管理
        </button>
        <button
          class="rounded-xl bg-primary-500 px-4 py-2 text-sm font-semibold text-white transition-colors hover:bg-primary-600"
          @click="showForm = !showForm"
        >
          {{ showForm ? '閉じる' : '追加' }}
        </button>
      </div>
    </div>

    <!-- Category manager -->
    <div
      v-if="showCategoryManager"
      class="mb-4 rounded-xl bg-white p-4 shadow-sm"
    >
      <h2 class="mb-3 text-sm font-bold text-gray-700">
        カテゴリ管理
      </h2>

      <!-- Error message -->
      <div
        v-if="categoryError"
        class="mb-3 rounded-lg bg-red-50 px-3 py-2 text-sm text-red-600"
      >
        {{ categoryError }}
      </div>

      <!-- Category list -->
      <div class="mb-3 space-y-2">
        <div
          v-for="cat in categories"
          :key="cat.id"
          class="flex items-center gap-2 rounded-lg border border-gray-100 p-2"
        >
          <template v-if="editingCategoryId === cat.id">
            <input
              v-model="editingCategoryColor"
              type="color"
              class="h-7 w-7 shrink-0 cursor-pointer rounded border-0 bg-transparent p-0"
            >
            <input
              v-model="editingCategoryName"
              type="text"
              class="min-w-0 flex-1 rounded-lg border border-gray-300 px-2 py-1 text-sm focus:border-primary-400 focus:outline-none focus:ring-1 focus:ring-primary-200"
              @keyup.enter="handleUpdateCategory(cat.id)"
            >
            <button
              class="shrink-0 rounded-lg px-2 py-1 text-xs font-medium text-primary-600 hover:bg-primary-50"
              @click="handleUpdateCategory(cat.id)"
            >
              保存
            </button>
            <button
              class="shrink-0 rounded-lg px-2 py-1 text-xs font-medium text-gray-500 hover:bg-gray-50"
              @click="cancelEditCategory"
            >
              取消
            </button>
          </template>
          <template v-else>
            <span
              class="h-4 w-4 shrink-0 rounded-full"
              :style="{ backgroundColor: cat.color ?? '#6366f1' }"
            />
            <span class="min-w-0 flex-1 text-sm text-gray-700">{{ cat.name }}</span>
            <button
              class="shrink-0 rounded-lg px-2 py-1 text-xs font-medium text-gray-500 hover:bg-gray-50"
              @click="startEditCategory(cat)"
            >
              編集
            </button>
            <button
              class="shrink-0 rounded-lg px-2 py-1 text-xs font-medium text-red-500 hover:bg-red-50"
              @click="handleDeleteCategory(cat.id)"
            >
              削除
            </button>
          </template>
        </div>
        <div
          v-if="categories.length === 0"
          class="py-4 text-center text-sm text-gray-400"
        >
          カテゴリがありません
        </div>
      </div>

      <!-- Add category form -->
      <div class="flex items-center gap-2">
        <input
          v-model="newCategoryColor"
          type="color"
          class="h-8 w-8 shrink-0 cursor-pointer rounded border-0 bg-transparent p-0"
        >
        <input
          v-model="newCategoryName"
          type="text"
          placeholder="新しいカテゴリ名"
          class="min-w-0 flex-1 rounded-xl border border-gray-300 px-3 py-2 text-sm focus:border-primary-400 focus:outline-none focus:ring-2 focus:ring-primary-200"
          @keyup.enter="handleCreateCategory"
        >
        <button
          :disabled="!newCategoryName.trim() || isCreatingCategory"
          class="shrink-0 rounded-xl bg-primary-500 px-3 py-2 text-sm font-semibold text-white transition-colors hover:bg-primary-600 disabled:bg-gray-300"
          @click="handleCreateCategory"
        >
          追加
        </button>
      </div>
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
          v-for="item in summaryCategories"
          :key="item.label"
          class="rounded-xl bg-white/15 px-2 py-1.5 text-center"
        >
          <p class="text-xs text-primary-100">
            {{ item.label }}
          </p>
          <p class="text-sm font-semibold">
            {{ formatAmount(item.amount) }}
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
            v-model.number="formData.expense_category_id"
            class="flex-1 rounded-xl border border-gray-300 bg-white px-3 py-2.5 text-sm focus:border-primary-400 focus:outline-none focus:ring-2 focus:ring-primary-200"
          >
            <option
              v-for="cat in categories"
              :key="cat.id"
              :value="cat.id"
            >
              {{ cat.name }}
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
        :class="!selectedCategoryId
          ? 'bg-primary-500 text-white shadow-sm'
          : 'bg-white text-gray-600 hover:bg-primary-50'"
        @click="selectedCategoryId = null"
      >
        全て
      </button>
      <button
        v-for="cat in categories"
        :key="cat.id"
        class="flex-shrink-0 rounded-full px-4 py-2 text-sm font-medium transition-colors"
        :class="selectedCategoryId === cat.id
          ? 'bg-primary-500 text-white shadow-sm'
          : 'bg-white text-gray-600 hover:bg-primary-50'"
        @click="selectedCategoryId = cat.id"
      >
        <span
          class="mr-1.5 inline-block h-2 w-2 rounded-full"
          :style="{ backgroundColor: cat.color ?? '#6366f1' }"
        />
        {{ cat.name }}
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
              <span
                class="rounded-full px-2 py-0.5 text-xs font-medium"
                :style="{
                  backgroundColor: (expense.category?.color ?? '#6366f1') + '15',
                  color: expense.category?.color ?? '#6366f1',
                }"
              >
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

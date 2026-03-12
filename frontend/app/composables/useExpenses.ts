import type { Expense, CreateExpenseInput, ExpenseSummary } from '~/types/expense'
import type { ApiResponse } from '~/types/auth'

export const useExpenses = (tripId: MaybeRefOrGetter<string | number>) => {
  const { apiFetch } = useApiClient()

  const fetchExpenses = (categoryId?: MaybeRefOrGetter<number | null>) => {
    const url = computed(() => {
      const t = toValue(tripId)
      const c = toValue(categoryId)
      return c ? `/api/trips/${t}/expenses?category_id=${c}` : `/api/trips/${t}/expenses`
    })
    return useApiFetch<ApiResponse<Expense[]>>(url)
  }

  const fetchSummary = () => {
    const url = computed(() => `/api/trips/${toValue(tripId)}/expenses/summary`)
    return useApiFetch<ApiResponse<ExpenseSummary>>(url)
  }

  const createExpense = async (input: CreateExpenseInput) => {
    const t = toValue(tripId)
    return apiFetch<ApiResponse<Expense>>(`/api/trips/${t}/expenses`, {
      method: 'POST',
      body: input,
    })
  }

  const deleteExpense = async (id: number) => {
    const t = toValue(tripId)
    return apiFetch(`/api/trips/${t}/expenses/${id}`, {
      method: 'DELETE',
    })
  }

  return { fetchExpenses, fetchSummary, createExpense, deleteExpense }
}

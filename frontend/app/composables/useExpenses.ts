import type { Expense, CreateExpenseInput, ExpenseSummary } from '~/types/expense'
import type { ApiResponse } from '~/types/auth'

export const useExpenses = (tripId: MaybeRefOrGetter<string | number>) => {
  const { apiFetch } = useApiClient()

  const fetchExpenses = (category?: MaybeRefOrGetter<string>) => {
    const url = computed(() => {
      const t = toValue(tripId)
      const c = toValue(category)
      return c ? `/api/trips/${t}/expenses?category=${c}` : `/api/trips/${t}/expenses`
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

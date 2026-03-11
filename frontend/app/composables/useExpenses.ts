import type { Expense, CreateExpenseInput, ExpenseSummary } from '~/types/expense'
import type { ApiResponse } from '~/types/auth'

export const useExpenses = () => {
  const { apiFetch } = useApiClient()

  const fetchExpenses = (category?: MaybeRefOrGetter<string>) => {
    const url = computed(() => {
      const c = toValue(category)
      return c ? `/api/expenses?category=${c}` : '/api/expenses'
    })
    return useApiFetch<ApiResponse<Expense[]>>(url)
  }

  const fetchSummary = () => {
    return useApiFetch<ApiResponse<ExpenseSummary>>('/api/expenses/summary')
  }

  const createExpense = async (input: CreateExpenseInput) => {
    return apiFetch<ApiResponse<Expense>>('/api/expenses', {
      method: 'POST',
      body: input,
    })
  }

  const deleteExpense = async (id: number) => {
    return apiFetch(`/api/expenses/${id}`, {
      method: 'DELETE',
    })
  }

  return { fetchExpenses, fetchSummary, createExpense, deleteExpense }
}

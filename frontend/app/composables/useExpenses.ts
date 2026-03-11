import type { Expense, CreateExpenseInput, ExpenseSummary } from '~/types/expense'
import type { ApiResponse } from '~/types/auth'

export const useExpenses = () => {
  const config = useRuntimeConfig()
  const baseURL = config.public.apiBase as string

  const fetchExpenses = (category?: string) => {
    const query = category ? `?category=${category}` : ''
    return useApiFetch<ApiResponse<Expense[]>>(`/api/expenses${query}`)
  }

  const fetchSummary = () => {
    return useApiFetch<ApiResponse<ExpenseSummary>>('/api/expenses/summary')
  }

  const createExpense = async (input: CreateExpenseInput) => {
    return $fetch<ApiResponse<Expense>>('/api/expenses', {
      baseURL,
      method: 'POST',
      body: input,
      credentials: 'include',
    })
  }

  const deleteExpense = async (id: number) => {
    return $fetch(`/api/expenses/${id}`, {
      baseURL,
      method: 'DELETE',
      credentials: 'include',
    })
  }

  return { fetchExpenses, fetchSummary, createExpense, deleteExpense }
}

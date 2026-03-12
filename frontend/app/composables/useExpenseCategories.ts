import type {
  ExpenseCategory,
  CreateExpenseCategoryInput,
  UpdateExpenseCategoryInput,
} from '~/types/expense'
import type { ApiResponse } from '~/types/auth'

export const useExpenseCategories = (tripId: MaybeRefOrGetter<string | number>) => {
  const { apiFetch } = useApiClient()

  const fetchCategories = () => {
    const url = computed(() => `/api/trips/${toValue(tripId)}/expense-categories`)
    return useApiFetch<ApiResponse<ExpenseCategory[]>>(url)
  }

  const createCategory = async (input: CreateExpenseCategoryInput) => {
    const t = toValue(tripId)
    return apiFetch<ApiResponse<ExpenseCategory>>(`/api/trips/${t}/expense-categories`, {
      method: 'POST',
      body: input,
    })
  }

  const updateCategory = async (id: number, input: UpdateExpenseCategoryInput) => {
    const t = toValue(tripId)
    return apiFetch<ApiResponse<ExpenseCategory>>(`/api/trips/${t}/expense-categories/${id}`, {
      method: 'PUT',
      body: input,
    })
  }

  const deleteCategory = async (id: number) => {
    const t = toValue(tripId)
    return apiFetch(`/api/trips/${t}/expense-categories/${id}`, {
      method: 'DELETE',
    })
  }

  return { fetchCategories, createCategory, updateCategory, deleteCategory }
}

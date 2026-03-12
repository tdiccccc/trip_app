export interface ExpenseCategory {
  id: number
  trip_id: number
  name: string
  key: string
  color: string | null
  sort_order: number
}

export interface CreateExpenseCategoryInput {
  name: string
  color?: string | null
}

export interface UpdateExpenseCategoryInput {
  name?: string
  color?: string | null
  sort_order?: number
}

export interface Expense {
  id: number
  trip_id: number
  user_id: number
  description: string
  amount: number
  expense_category_id: number
  category: ExpenseCategory
  paid_at: string
  is_shared: boolean
}

export interface CreateExpenseInput {
  description: string
  amount: number
  expense_category_id: number
  paid_at?: string
  is_shared?: boolean
}

export interface ExpenseSummary {
  total: number
  per_person: number
  by_category: Record<string, number>
}

export interface Expense {
  id: number
  trip_id: number
  user_id: number
  description: string
  amount: number
  category: string
  paid_at: string
  is_shared: boolean
}

export interface CreateExpenseInput {
  description: string
  amount: number
  category: string
  paid_at?: string
  is_shared?: boolean
}

export interface ExpenseSummary {
  total: number
  per_person: number
  by_category: Record<string, number>
}

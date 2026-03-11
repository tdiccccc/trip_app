export interface Expense {
  id: number
  trip_id: number
  label: string
  amount: number
  category: string
  paid_by: string
  note: string | null
  created_at: string
}

export interface CreateExpenseInput {
  label: string
  amount: number
  category: string
  paid_by: string
  note?: string
}

export interface ExpenseSummary {
  total: number
  per_person: number
  by_category: Record<string, number>
}

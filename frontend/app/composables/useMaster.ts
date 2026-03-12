interface MasterItem {
  key: string
  label: string
}

// Fallback data when API is unavailable
const FALLBACK_EXPENSE_CATEGORIES: MasterItem[] = [
  { key: 'food', label: '食事' },
  { key: 'transport', label: '交通' },
  { key: 'souvenir', label: 'お土産' },
  { key: 'accommodation', label: '宿泊' },
  { key: 'other', label: 'その他' },
]

const FALLBACK_ASSIGNEES: MasterItem[] = [
  { key: 'self', label: '自分' },
  { key: 'partner', label: 'パートナー' },
  { key: 'shared', label: '共有' },
]

export const useMaster = () => {
  const fetchExpenseCategories = () => {
    return useApiFetch<{ data: MasterItem[] }>('/api/master/expense-categories')
  }

  const fetchAssignees = () => {
    return useApiFetch<{ data: MasterItem[] }>('/api/master/assignees')
  }

  return {
    fetchExpenseCategories,
    fetchAssignees,
    FALLBACK_EXPENSE_CATEGORIES,
    FALLBACK_ASSIGNEES,
  }
}

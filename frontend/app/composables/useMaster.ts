interface MasterItem {
  key: string
  label: string
}

const FALLBACK_ASSIGNEES: MasterItem[] = [
  { key: 'self', label: '自分' },
  { key: 'partner', label: 'パートナー' },
  { key: 'shared', label: '共有' },
]

export const useMaster = () => {
  const fetchAssignees = () => {
    return useApiFetch<{ data: MasterItem[] }>('/api/master/assignees')
  }

  return {
    fetchAssignees,
    FALLBACK_ASSIGNEES,
  }
}

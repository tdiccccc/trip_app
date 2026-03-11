export interface PackingItem {
  id: number
  name: string
  assignee: string | null
  is_checked: boolean
}

export interface CreatePackingItemInput {
  name: string
  assignee?: string
}

export interface UpdatePackingItemInput {
  name?: string
  assignee?: string
  is_checked?: boolean
}

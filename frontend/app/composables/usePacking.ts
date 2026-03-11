import type { PackingItem, CreatePackingItemInput, UpdatePackingItemInput } from '~/types/packing'
import type { ApiResponse } from '~/types/auth'

export const usePacking = () => {
  const { apiFetch } = useApiClient()

  const fetchItems = (assignee?: string) => {
    const query = assignee ? `?assignee=${assignee}` : ''
    return useApiFetch<ApiResponse<PackingItem[]>>(`/api/packing${query}`)
  }

  const createItem = async (input: CreatePackingItemInput) => {
    return apiFetch<ApiResponse<PackingItem>>('/api/packing', {
      method: 'POST',
      body: input,
    })
  }

  const updateItem = async (id: number, input: UpdatePackingItemInput) => {
    return apiFetch<ApiResponse<PackingItem>>(`/api/packing/${id}`, {
      method: 'PATCH',
      body: input,
    })
  }

  const deleteItem = async (id: number) => {
    return apiFetch(`/api/packing/${id}`, {
      method: 'DELETE',
    })
  }

  return { fetchItems, createItem, updateItem, deleteItem }
}

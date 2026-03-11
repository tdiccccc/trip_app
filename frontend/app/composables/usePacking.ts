import type { PackingItem, CreatePackingItemInput, UpdatePackingItemInput } from '~/types/packing'
import type { ApiResponse } from '~/types/auth'

export const usePacking = () => {
  const config = useRuntimeConfig()
  const baseURL = config.public.apiBase as string

  const fetchItems = (assignee?: string) => {
    const query = assignee ? `?assignee=${assignee}` : ''
    return useApiFetch<ApiResponse<PackingItem[]>>(`/api/packing${query}`)
  }

  const createItem = async (input: CreatePackingItemInput) => {
    return $fetch<ApiResponse<PackingItem>>('/api/packing', {
      baseURL,
      method: 'POST',
      body: input,
      credentials: 'include',
    })
  }

  const updateItem = async (id: number, input: UpdatePackingItemInput) => {
    return $fetch<ApiResponse<PackingItem>>(`/api/packing/${id}`, {
      baseURL,
      method: 'PATCH',
      body: input,
      credentials: 'include',
    })
  }

  const deleteItem = async (id: number) => {
    return $fetch(`/api/packing/${id}`, {
      baseURL,
      method: 'DELETE',
      credentials: 'include',
    })
  }

  return { fetchItems, createItem, updateItem, deleteItem }
}

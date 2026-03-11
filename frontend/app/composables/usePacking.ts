import type { PackingItem, CreatePackingItemInput, UpdatePackingItemInput } from '~/types/packing'
import type { ApiResponse } from '~/types/auth'

export const usePacking = (tripId: MaybeRefOrGetter<string | number>) => {
  const { apiFetch } = useApiClient()

  const fetchItems = (assignee?: string) => {
    const url = computed(() => {
      const t = toValue(tripId)
      const query = assignee ? `?assignee=${assignee}` : ''
      return `/api/trips/${t}/packing${query}`
    })
    return useApiFetch<ApiResponse<PackingItem[]>>(url)
  }

  const createItem = async (input: CreatePackingItemInput) => {
    const t = toValue(tripId)
    return apiFetch<ApiResponse<PackingItem>>(`/api/trips/${t}/packing`, {
      method: 'POST',
      body: input,
    })
  }

  const updateItem = async (id: number, input: UpdatePackingItemInput) => {
    const t = toValue(tripId)
    return apiFetch<ApiResponse<PackingItem>>(`/api/trips/${t}/packing/${id}`, {
      method: 'PATCH',
      body: input,
    })
  }

  const deleteItem = async (id: number) => {
    const t = toValue(tripId)
    return apiFetch(`/api/trips/${t}/packing/${id}`, {
      method: 'DELETE',
    })
  }

  return { fetchItems, createItem, updateItem, deleteItem }
}

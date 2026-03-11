import type { ItineraryItem, CreateItineraryItemInput } from '~/types/itinerary'
import type { ApiResponse } from '~/types/auth'

export const useItinerary = () => {
  const { apiFetch } = useApiClient()

  const fetchItems = (date?: MaybeRefOrGetter<string>) => {
    const url = computed(() => {
      const d = toValue(date)
      return d ? `/api/itinerary?date=${d}` : '/api/itinerary'
    })
    return useApiFetch<ApiResponse<ItineraryItem[]>>(url)
  }

  const createItem = async (input: CreateItineraryItemInput) => {
    return apiFetch<ApiResponse<ItineraryItem>>('/api/itinerary', {
      method: 'POST',
      body: input,
    })
  }

  const updateItem = async (id: number, input: Partial<CreateItineraryItemInput>) => {
    return apiFetch<ApiResponse<ItineraryItem>>(`/api/itinerary/${id}`, {
      method: 'PATCH',
      body: input,
    })
  }

  const deleteItem = async (id: number) => {
    return apiFetch(`/api/itinerary/${id}`, {
      method: 'DELETE',
    })
  }

  const reorderItems = async (items: { id: number; sort_order: number }[]) => {
    return apiFetch<ApiResponse<ItineraryItem[]>>('/api/itinerary/reorder', {
      method: 'PATCH',
      body: { items },
    })
  }

  return { fetchItems, createItem, updateItem, deleteItem, reorderItems }
}

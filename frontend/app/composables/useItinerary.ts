import type { ItineraryItem, CreateItineraryItemInput } from '~/types/itinerary'
import type { ApiResponse } from '~/types/auth'

export const useItinerary = (tripId: MaybeRefOrGetter<string | number>) => {
  const { apiFetch } = useApiClient()

  const fetchItems = (date?: MaybeRefOrGetter<string>) => {
    const url = computed(() => {
      const t = toValue(tripId)
      const d = toValue(date)
      return d ? `/api/trips/${t}/itinerary?date=${d}` : `/api/trips/${t}/itinerary`
    })
    return useApiFetch<ApiResponse<ItineraryItem[]>>(url)
  }

  const createItem = async (input: CreateItineraryItemInput) => {
    const t = toValue(tripId)
    return apiFetch<ApiResponse<ItineraryItem>>(`/api/trips/${t}/itinerary`, {
      method: 'POST',
      body: input,
    })
  }

  const updateItem = async (id: number, input: Partial<CreateItineraryItemInput>) => {
    const t = toValue(tripId)
    return apiFetch<ApiResponse<ItineraryItem>>(`/api/trips/${t}/itinerary/${id}`, {
      method: 'PATCH',
      body: input,
    })
  }

  const deleteItem = async (id: number) => {
    const t = toValue(tripId)
    return apiFetch(`/api/trips/${t}/itinerary/${id}`, {
      method: 'DELETE',
    })
  }

  const reorderItems = async (items: { id: number; sort_order: number }[]) => {
    const t = toValue(tripId)
    return apiFetch<ApiResponse<ItineraryItem[]>>(`/api/trips/${t}/itinerary/reorder`, {
      method: 'PATCH',
      body: { items },
    })
  }

  return { fetchItems, createItem, updateItem, deleteItem, reorderItems }
}

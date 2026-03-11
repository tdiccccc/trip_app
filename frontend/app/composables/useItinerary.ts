import type { ItineraryItem, CreateItineraryItemInput } from '~/types/itinerary'
import type { ApiResponse } from '~/types/auth'

export const useItinerary = () => {
  const config = useRuntimeConfig()
  const baseURL = config.public.apiBase as string

  const fetchItems = (date?: string) => {
    const query = date ? `?date=${date}` : ''
    return useApiFetch<ApiResponse<ItineraryItem[]>>(`/api/itinerary${query}`)
  }

  const createItem = async (input: CreateItineraryItemInput) => {
    return $fetch<ApiResponse<ItineraryItem>>('/api/itinerary', {
      baseURL,
      method: 'POST',
      body: input,
      credentials: 'include',
    })
  }

  const updateItem = async (id: number, input: Partial<CreateItineraryItemInput>) => {
    return $fetch<ApiResponse<ItineraryItem>>(`/api/itinerary/${id}`, {
      baseURL,
      method: 'PATCH',
      body: input,
      credentials: 'include',
    })
  }

  const deleteItem = async (id: number) => {
    return $fetch(`/api/itinerary/${id}`, {
      baseURL,
      method: 'DELETE',
      credentials: 'include',
    })
  }

  const reorderItems = async (items: { id: number; sort_order: number }[]) => {
    return $fetch<ApiResponse<ItineraryItem[]>>('/api/itinerary/reorder', {
      baseURL,
      method: 'PATCH',
      body: { items },
      credentials: 'include',
    })
  }

  return { fetchItems, createItem, updateItem, deleteItem, reorderItems }
}

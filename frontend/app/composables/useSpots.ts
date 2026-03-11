import type { Spot, SpotDetail, SpotMemo } from '~/types/spot'
import type { ApiResponse } from '~/types/auth'

export const useSpots = (tripId: MaybeRefOrGetter<string | number>) => {
  const { apiFetch } = useApiClient()

  const fetchSpots = (category?: string) => {
    const url = computed(() => {
      const t = toValue(tripId)
      const query = category ? `?category=${category}` : ''
      return `/api/trips/${t}/spots${query}`
    })
    return useApiFetch<ApiResponse<Spot[]>>(url)
  }

  const fetchSpot = (id: MaybeRefOrGetter<number | string>) => {
    const url = computed(() => {
      const t = toValue(tripId)
      const spotId = toValue(id)
      return `/api/trips/${t}/spots/${spotId}`
    })
    return useApiFetch<ApiResponse<SpotDetail>>(url)
  }

  const createMemo = async (spotId: number, body: string) => {
    const t = toValue(tripId)
    return apiFetch<ApiResponse<SpotMemo>>(`/api/trips/${t}/spots/${spotId}/memos`, {
      method: 'POST',
      body: { body },
    })
  }

  return { fetchSpots, fetchSpot, createMemo }
}

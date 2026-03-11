import type { Spot, SpotDetail, SpotMemo } from '~/types/spot'
import type { ApiResponse } from '~/types/auth'

export const useSpots = () => {
  const { apiFetch } = useApiClient()

  const fetchSpots = (category?: string) => {
    const query = category ? `?category=${category}` : ''
    return useApiFetch<ApiResponse<Spot[]>>(`/api/spots${query}`)
  }

  const fetchSpot = (id: number | string) => {
    return useApiFetch<ApiResponse<SpotDetail>>(`/api/spots/${id}`)
  }

  const createMemo = async (spotId: number, body: string) => {
    return apiFetch<ApiResponse<SpotMemo>>(`/api/spots/${spotId}/memos`, {
      method: 'POST',
      body: { body },
    })
  }

  return { fetchSpots, fetchSpot, createMemo }
}

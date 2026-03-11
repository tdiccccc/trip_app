import type { Spot, SpotDetail, SpotMemo } from '~/types/spot'
import type { ApiResponse } from '~/types/auth'

export const useSpots = () => {
  const fetchSpots = (category?: string) => {
    const query = category ? `?category=${category}` : ''
    return useApiFetch<ApiResponse<Spot[]>>(`/api/spots${query}`)
  }

  const fetchSpot = (id: number | string) => {
    return useApiFetch<ApiResponse<SpotDetail>>(`/api/spots/${id}`)
  }

  const createMemo = async (spotId: number, body: string) => {
    const config = useRuntimeConfig()
    return $fetch<ApiResponse<SpotMemo>>(`/api/spots/${spotId}/memos`, {
      baseURL: config.public.apiBase as string,
      method: 'POST',
      body: { body },
      credentials: 'include',
    })
  }

  return { fetchSpots, fetchSpot, createMemo }
}

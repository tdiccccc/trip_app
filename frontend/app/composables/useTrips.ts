import type { Trip, CreateTripRequest, UpdateTripRequest } from '~/types/trip'
import type { ApiResponse } from '~/types/auth'

export const useTrips = () => {
  const { apiFetch } = useApiClient()

  const fetchTrips = () => {
    return useApiFetch<ApiResponse<Trip[]>>('/api/trips')
  }

  const fetchTrip = (tripId: MaybeRefOrGetter<string | number>) => {
    const url = computed(() => `/api/trips/${toValue(tripId)}`)
    return useApiFetch<ApiResponse<Trip>>(url)
  }

  const createTrip = async (input: CreateTripRequest) => {
    return apiFetch<ApiResponse<Trip>>('/api/trips', {
      method: 'POST',
      body: input,
    })
  }

  const updateTrip = async (tripId: number | string, input: UpdateTripRequest) => {
    return apiFetch<ApiResponse<Trip>>(`/api/trips/${tripId}`, {
      method: 'PATCH',
      body: input,
    })
  }

  const deleteTrip = async (tripId: number | string) => {
    return apiFetch(`/api/trips/${tripId}`, {
      method: 'DELETE',
    })
  }

  return { fetchTrips, fetchTrip, createTrip, updateTrip, deleteTrip }
}

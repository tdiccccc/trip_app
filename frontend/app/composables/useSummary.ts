import type { TripSummary } from '~/types/summary'
import type { ApiResponse } from '~/types/auth'

export const useSummary = (tripId: MaybeRefOrGetter<string | number>) => {
  const fetchSummary = () => {
    const url = computed(() => `/api/trips/${toValue(tripId)}/summary`)
    return useApiFetch<ApiResponse<TripSummary>>(url)
  }

  return { fetchSummary }
}

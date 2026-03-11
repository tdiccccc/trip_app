import type { BoardPost, CreateBoardPostInput, CreateReactionInput, Reaction } from '~/types/board'
import type { ApiResponse } from '~/types/auth'

export const useBoard = (tripId: MaybeRefOrGetter<string | number>) => {
  const { apiFetch } = useApiClient()

  const fetchPosts = () => {
    const url = computed(() => `/api/trips/${toValue(tripId)}/board`)
    return useApiFetch<ApiResponse<BoardPost[]>>(url)
  }

  const createPost = async (input: CreateBoardPostInput) => {
    const t = toValue(tripId)
    return apiFetch<ApiResponse<BoardPost>>(`/api/trips/${t}/board`, {
      method: 'POST',
      body: input,
    })
  }

  const addReaction = async (postId: number, input: CreateReactionInput) => {
    const t = toValue(tripId)
    return apiFetch<ApiResponse<Reaction>>(`/api/trips/${t}/board/${postId}/reactions`, {
      method: 'POST',
      body: input,
    })
  }

  return { fetchPosts, createPost, addReaction }
}

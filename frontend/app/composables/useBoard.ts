import type { BoardPost, CreateBoardPostInput, CreateReactionInput, Reaction } from '~/types/board'
import type { ApiResponse } from '~/types/auth'

export const useBoard = () => {
  const { apiFetch } = useApiClient()

  const fetchPosts = () => {
    return useApiFetch<ApiResponse<BoardPost[]>>('/api/board')
  }

  const createPost = async (input: CreateBoardPostInput) => {
    return apiFetch<ApiResponse<BoardPost>>('/api/board', {
      method: 'POST',
      body: input,
    })
  }

  const addReaction = async (postId: number, input: CreateReactionInput) => {
    return apiFetch<ApiResponse<Reaction>>(`/api/board/${postId}/reactions`, {
      method: 'POST',
      body: input,
    })
  }

  return { fetchPosts, createPost, addReaction }
}

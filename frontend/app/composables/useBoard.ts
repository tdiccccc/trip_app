import type { BoardPost, CreateBoardPostInput, CreateReactionInput, Reaction } from '~/types/board'
import type { ApiResponse } from '~/types/auth'

export const useBoard = () => {
  const config = useRuntimeConfig()
  const baseURL = config.public.apiBase as string

  const fetchPosts = () => {
    return useApiFetch<ApiResponse<BoardPost[]>>('/api/board')
  }

  const createPost = async (input: CreateBoardPostInput) => {
    return $fetch<ApiResponse<BoardPost>>('/api/board', {
      baseURL,
      method: 'POST',
      body: input,
      credentials: 'include',
    })
  }

  const addReaction = async (postId: number, input: CreateReactionInput) => {
    return $fetch<ApiResponse<Reaction>>(`/api/board/${postId}/reactions`, {
      baseURL,
      method: 'POST',
      body: input,
      credentials: 'include',
    })
  }

  return { fetchPosts, createPost, addReaction }
}

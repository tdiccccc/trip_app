import { describe, it, expect, vi, beforeEach } from 'vitest'

const mockUseApiFetch = vi.fn()
vi.mock('~/composables/useApiFetch', () => ({
  useApiFetch: (...args: unknown[]) => mockUseApiFetch(...args),
}))

const mockFetch = vi.fn()
vi.stubGlobal('$fetch', mockFetch)

import { useBoard } from '~/composables/useBoard'

describe('useBoard', () => {
  beforeEach(() => {
    vi.clearAllMocks()
    mockUseApiFetch.mockReturnValue({ data: ref(null), error: ref(null) })
  })

  it('fetchPosts calls useApiFetch with correct path', () => {
    const { fetchPosts } = useBoard(1)
    fetchPosts()

    expect(mockUseApiFetch).toHaveBeenCalled()
    const arg = mockUseApiFetch.mock.calls[0]![0]
    expect(toValue(arg)).toBe('/api/trips/1/board')
  })

  it('createPost sends POST request with body', async () => {
    const input = { body: 'Hello from Ise!' }
    mockFetch.mockResolvedValueOnce({ data: { id: 1, ...input } })

    const { createPost } = useBoard(1)
    await createPost(input)

    expect(mockFetch).toHaveBeenCalledWith('/api/trips/1/board', expect.objectContaining({
      method: 'POST',
      body: input,
      credentials: 'include',
    }))
  })

  it('addReaction sends POST request with emoji', async () => {
    const input = { emoji: '👍' }
    mockFetch.mockResolvedValueOnce({ data: { id: 1, user_id: 1, emoji: '👍' } })

    const { addReaction } = useBoard(1)
    await addReaction(42, input)

    expect(mockFetch).toHaveBeenCalledWith('/api/trips/1/board/42/reactions', expect.objectContaining({
      method: 'POST',
      body: input,
      credentials: 'include',
    }))
  })
})

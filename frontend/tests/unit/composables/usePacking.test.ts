import { describe, it, expect, vi, beforeEach } from 'vitest'

const mockUseApiFetch = vi.fn()
vi.mock('~/composables/useApiFetch', () => ({
  useApiFetch: (...args: unknown[]) => mockUseApiFetch(...args),
}))

const mockFetch = vi.fn()
vi.stubGlobal('$fetch', mockFetch)

import { usePacking } from '~/composables/usePacking'

describe('usePacking', () => {
  beforeEach(() => {
    vi.clearAllMocks()
    mockUseApiFetch.mockReturnValue({ data: ref(null), error: ref(null) })
  })

  it('fetchItems calls useApiFetch without filter', () => {
    const { fetchItems } = usePacking()
    fetchItems()

    expect(mockUseApiFetch).toHaveBeenCalledWith('/api/packing')
  })

  it('fetchItems calls useApiFetch with assignee filter', () => {
    const { fetchItems } = usePacking()
    fetchItems('Alice')

    expect(mockUseApiFetch).toHaveBeenCalledWith('/api/packing?assignee=Alice')
  })

  it('createItem sends POST request', async () => {
    const input = { name: 'Sunscreen', assignee: 'Alice' }
    mockFetch.mockResolvedValueOnce({ data: { id: 1, ...input, is_checked: false } })

    const { createItem } = usePacking()
    await createItem(input)

    expect(mockFetch).toHaveBeenCalledWith('/api/packing', expect.objectContaining({
      method: 'POST',
      body: input,
      credentials: 'include',
    }))
  })

  it('updateItem sends PATCH request', async () => {
    const input = { is_checked: true }
    mockFetch.mockResolvedValueOnce({ data: { id: 1, name: 'Sunscreen', is_checked: true } })

    const { updateItem } = usePacking()
    await updateItem(1, input)

    expect(mockFetch).toHaveBeenCalledWith('/api/packing/1', expect.objectContaining({
      method: 'PATCH',
      body: input,
      credentials: 'include',
    }))
  })

  it('deleteItem sends DELETE request', async () => {
    mockFetch.mockResolvedValueOnce(undefined)

    const { deleteItem } = usePacking()
    await deleteItem(3)

    expect(mockFetch).toHaveBeenCalledWith('/api/packing/3', expect.objectContaining({
      method: 'DELETE',
      credentials: 'include',
    }))
  })
})

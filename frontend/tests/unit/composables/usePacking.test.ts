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
    const { fetchItems } = usePacking(1)
    fetchItems()

    expect(mockUseApiFetch).toHaveBeenCalled()
    const arg = mockUseApiFetch.mock.calls[0]![0]
    expect(toValue(arg)).toBe('/api/trips/1/packing')
  })

  it('fetchItems calls useApiFetch with assignee filter', () => {
    const { fetchItems } = usePacking(1)
    fetchItems('Alice')

    expect(mockUseApiFetch).toHaveBeenCalled()
    const arg = mockUseApiFetch.mock.calls[0]![0]
    expect(toValue(arg)).toBe('/api/trips/1/packing?assignee=Alice')
  })

  it('createItem sends POST request', async () => {
    const input = { name: 'Sunscreen', assignee: 'Alice' }
    mockFetch.mockResolvedValueOnce({ data: { id: 1, ...input, is_checked: false } })

    const { createItem } = usePacking(1)
    await createItem(input)

    expect(mockFetch).toHaveBeenCalledWith('/api/trips/1/packing', expect.objectContaining({
      method: 'POST',
      body: input,
      credentials: 'include',
    }))
  })

  it('updateItem sends PATCH request', async () => {
    const input = { is_checked: true }
    mockFetch.mockResolvedValueOnce({ data: { id: 1, name: 'Sunscreen', is_checked: true } })

    const { updateItem } = usePacking(1)
    await updateItem(1, input)

    expect(mockFetch).toHaveBeenCalledWith('/api/trips/1/packing/1', expect.objectContaining({
      method: 'PATCH',
      body: input,
      credentials: 'include',
    }))
  })

  it('deleteItem sends DELETE request', async () => {
    mockFetch.mockResolvedValueOnce(undefined)

    const { deleteItem } = usePacking(1)
    await deleteItem(3)

    expect(mockFetch).toHaveBeenCalledWith('/api/trips/1/packing/3', expect.objectContaining({
      method: 'DELETE',
      credentials: 'include',
    }))
  })
})

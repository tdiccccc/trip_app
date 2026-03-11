import { describe, it, expect, vi, beforeEach } from 'vitest'

const mockUseApiFetch = vi.fn()
vi.mock('~/composables/useApiFetch', () => ({
  useApiFetch: (...args: unknown[]) => mockUseApiFetch(...args),
}))

const mockFetch = vi.fn()
vi.stubGlobal('$fetch', mockFetch)

import { useItinerary } from '~/composables/useItinerary'

describe('useItinerary', () => {
  beforeEach(() => {
    vi.clearAllMocks()
    mockUseApiFetch.mockReturnValue({ data: ref(null), error: ref(null) })
  })

  it('fetchItems calls useApiFetch without date filter', () => {
    const { fetchItems } = useItinerary(1)
    fetchItems()

    expect(mockUseApiFetch).toHaveBeenCalled()
    const arg = mockUseApiFetch.mock.calls[0]![0]
    expect(toValue(arg)).toBe('/api/trips/1/itinerary')
  })

  it('fetchItems calls useApiFetch with date filter', () => {
    const { fetchItems } = useItinerary(1)
    fetchItems('2026-03-28')

    expect(mockUseApiFetch).toHaveBeenCalled()
    const arg = mockUseApiFetch.mock.calls[0]![0]
    expect(toValue(arg)).toBe('/api/trips/1/itinerary?date=2026-03-28')
  })

  it('createItem posts data via $fetch', async () => {
    const input = {
      title: 'Visit Ise Jingu',
      date: '2026-03-28',
      start_time: '10:00',
    }
    mockFetch.mockResolvedValueOnce({ data: { id: 1, ...input } })

    const { createItem } = useItinerary(1)
    await createItem(input)

    expect(mockFetch).toHaveBeenCalledWith('/api/trips/1/itinerary', expect.objectContaining({
      method: 'POST',
      body: input,
      credentials: 'include',
    }))
  })

  it('updateItem patches data via $fetch', async () => {
    const input = { title: 'Updated title' }
    mockFetch.mockResolvedValueOnce({ data: { id: 1, ...input } })

    const { updateItem } = useItinerary(1)
    await updateItem(1, input)

    expect(mockFetch).toHaveBeenCalledWith('/api/trips/1/itinerary/1', expect.objectContaining({
      method: 'PATCH',
      body: input,
      credentials: 'include',
    }))
  })

  it('deleteItem sends DELETE request', async () => {
    mockFetch.mockResolvedValueOnce(undefined)

    const { deleteItem } = useItinerary(1)
    await deleteItem(5)

    expect(mockFetch).toHaveBeenCalledWith('/api/trips/1/itinerary/5', expect.objectContaining({
      method: 'DELETE',
      credentials: 'include',
    }))
  })

  it('reorderItems sends PATCH with items array', async () => {
    const items = [
      { id: 1, sort_order: 0 },
      { id: 2, sort_order: 1 },
    ]
    mockFetch.mockResolvedValueOnce({ data: [] })

    const { reorderItems } = useItinerary(1)
    await reorderItems(items)

    expect(mockFetch).toHaveBeenCalledWith('/api/trips/1/itinerary/reorder', expect.objectContaining({
      method: 'PATCH',
      body: { items },
      credentials: 'include',
    }))
  })
})

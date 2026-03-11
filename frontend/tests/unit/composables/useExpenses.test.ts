import { describe, it, expect, vi, beforeEach } from 'vitest'

const mockUseApiFetch = vi.fn()
vi.mock('~/composables/useApiFetch', () => ({
  useApiFetch: (...args: unknown[]) => mockUseApiFetch(...args),
}))

const mockFetch = vi.fn()
vi.stubGlobal('$fetch', mockFetch)

import { useExpenses } from '~/composables/useExpenses'

describe('useExpenses', () => {
  beforeEach(() => {
    vi.clearAllMocks()
    mockUseApiFetch.mockReturnValue({ data: ref(null), error: ref(null) })
  })

  it('fetchExpenses calls useApiFetch without filter', () => {
    const { fetchExpenses } = useExpenses(1)
    fetchExpenses()

    expect(mockUseApiFetch).toHaveBeenCalled()
    const arg = mockUseApiFetch.mock.calls[0]![0]
    expect(toValue(arg)).toBe('/api/trips/1/expenses')
  })

  it('fetchExpenses calls useApiFetch with category filter', () => {
    const { fetchExpenses } = useExpenses(1)
    fetchExpenses('food')

    expect(mockUseApiFetch).toHaveBeenCalled()
    const arg = mockUseApiFetch.mock.calls[0]![0]
    expect(toValue(arg)).toBe('/api/trips/1/expenses?category=food')
  })

  it('fetchSummary calls useApiFetch with summary path', () => {
    const { fetchSummary } = useExpenses(1)
    fetchSummary()

    expect(mockUseApiFetch).toHaveBeenCalled()
    const arg = mockUseApiFetch.mock.calls[0]![0]
    expect(toValue(arg)).toBe('/api/trips/1/expenses/summary')
  })

  it('createExpense sends POST request', async () => {
    const input = {
      label: 'Lunch at Okage Yokocho',
      amount: 1500,
      category: 'food',
      paid_by: 'Alice',
    }
    mockFetch.mockResolvedValueOnce({ data: { id: 1, ...input } })

    const { createExpense } = useExpenses(1)
    await createExpense(input)

    expect(mockFetch).toHaveBeenCalledWith('/api/trips/1/expenses', expect.objectContaining({
      method: 'POST',
      body: input,
      credentials: 'include',
    }))
  })

  it('deleteExpense sends DELETE request', async () => {
    mockFetch.mockResolvedValueOnce(undefined)

    const { deleteExpense } = useExpenses(1)
    await deleteExpense(7)

    expect(mockFetch).toHaveBeenCalledWith('/api/trips/1/expenses/7', expect.objectContaining({
      method: 'DELETE',
      credentials: 'include',
    }))
  })
})

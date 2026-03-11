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
    const { fetchExpenses } = useExpenses()
    fetchExpenses()

    expect(mockUseApiFetch).toHaveBeenCalledWith('/api/expenses')
  })

  it('fetchExpenses calls useApiFetch with category filter', () => {
    const { fetchExpenses } = useExpenses()
    fetchExpenses('food')

    expect(mockUseApiFetch).toHaveBeenCalledWith('/api/expenses?category=food')
  })

  it('fetchSummary calls useApiFetch with summary path', () => {
    const { fetchSummary } = useExpenses()
    fetchSummary()

    expect(mockUseApiFetch).toHaveBeenCalledWith('/api/expenses/summary')
  })

  it('createExpense sends POST request', async () => {
    const input = {
      label: 'Lunch at Okage Yokocho',
      amount: 1500,
      category: 'food',
      paid_by: 'Alice',
    }
    mockFetch.mockResolvedValueOnce({ data: { id: 1, ...input } })

    const { createExpense } = useExpenses()
    await createExpense(input)

    expect(mockFetch).toHaveBeenCalledWith('/api/expenses', expect.objectContaining({
      method: 'POST',
      body: input,
      credentials: 'include',
    }))
  })

  it('deleteExpense sends DELETE request', async () => {
    mockFetch.mockResolvedValueOnce(undefined)

    const { deleteExpense } = useExpenses()
    await deleteExpense(7)

    expect(mockFetch).toHaveBeenCalledWith('/api/expenses/7', expect.objectContaining({
      method: 'DELETE',
      credentials: 'include',
    }))
  })
})

import { describe, it, expect, vi, beforeEach } from 'vitest'
import { mockNuxtImport } from '@nuxt/test-utils/runtime'

const mockFetch = vi.fn()
vi.stubGlobal('$fetch', mockFetch)

const { navigateToMock } = vi.hoisted(() => {
  return { navigateToMock: vi.fn() }
})

mockNuxtImport('navigateTo', () => navigateToMock)

import { useAuth } from '~/composables/useAuth'

describe('useAuth', () => {
  beforeEach(() => {
    vi.clearAllMocks()
    useState('auth-user').value = null
  })

  it('initializes with null user and isAuthenticated false', () => {
    const { user, isAuthenticated } = useAuth()
    expect(user.value).toBeNull()
    expect(isAuthenticated.value).toBe(false)
  })

  it('login fetches csrf cookie then authenticates', async () => {
    const mockUser = { id: 1, name: 'Test', email: 'test@example.com' }
    mockFetch
      .mockResolvedValueOnce(undefined) // csrf cookie
      .mockResolvedValueOnce({ data: mockUser }) // login

    const { login, user, isAuthenticated } = useAuth()
    await login('test@example.com', 'password123')

    expect(mockFetch).toHaveBeenCalledTimes(2)
    expect(mockFetch).toHaveBeenNthCalledWith(1, '/sanctum/csrf-cookie', expect.objectContaining({
      credentials: 'include',
    }))
    expect(mockFetch).toHaveBeenNthCalledWith(2, '/api/login', expect.objectContaining({
      method: 'POST',
      body: { email: 'test@example.com', password: 'password123' },
      credentials: 'include',
    }))
    expect(user.value).toEqual(mockUser)
    expect(isAuthenticated.value).toBe(true)
  })

  it('logout clears user and navigates to login', async () => {
    const mockUser = { id: 1, name: 'Test', email: 'test@example.com' }
    useState('auth-user').value = mockUser
    mockFetch.mockResolvedValueOnce(undefined)

    const { logout, user } = useAuth()
    await logout()

    expect(mockFetch).toHaveBeenCalledWith('/api/logout', expect.objectContaining({
      method: 'POST',
      credentials: 'include',
    }))
    expect(user.value).toBeNull()
    expect(navigateToMock).toHaveBeenCalledWith('/login')
  })

  it('logout clears user even if API fails', async () => {
    useState('auth-user').value = { id: 1, name: 'Test', email: 'test@example.com' }
    mockFetch.mockRejectedValueOnce(new Error('Network error'))

    const { logout, user } = useAuth()
    await logout()

    expect(user.value).toBeNull()
    expect(navigateToMock).toHaveBeenCalledWith('/login')
  })

  it('fetchUser sets user on success', async () => {
    const mockUser = { id: 1, name: 'Test', email: 'test@example.com' }
    mockFetch
      .mockResolvedValueOnce(undefined) // csrf cookie
      .mockResolvedValueOnce({ data: mockUser }) // api/user

    const { fetchUser, user } = useAuth()
    await fetchUser()

    expect(mockFetch).toHaveBeenCalledWith('/api/user', expect.objectContaining({
      credentials: 'include',
    }))
    expect(user.value).toEqual(mockUser)
  })

  it('fetchUser clears user on failure', async () => {
    useState('auth-user').value = { id: 1, name: 'Test', email: 'test@example.com' }
    mockFetch
      .mockResolvedValueOnce(undefined) // csrf cookie
      .mockRejectedValueOnce(new Error('Unauthorized')) // api/user

    const { fetchUser, user } = useAuth()
    await fetchUser()

    expect(user.value).toBeNull()
  })
})

import { describe, it, expect, vi } from 'vitest'
import { mountSuspended } from '@nuxt/test-utils/runtime'
import LoginPage from '~/pages/login.vue'

vi.stubGlobal('navigateTo', vi.fn())

describe('login page', () => {
  it('renders email input', async () => {
    const wrapper = await mountSuspended(LoginPage)

    const emailInput = wrapper.find('input[type="email"]')
    expect(emailInput.exists()).toBe(true)
    expect(emailInput.attributes('id')).toBe('email')
  })

  it('renders password input', async () => {
    const wrapper = await mountSuspended(LoginPage)

    const passwordInput = wrapper.find('input[type="password"]')
    expect(passwordInput.exists()).toBe(true)
    expect(passwordInput.attributes('id')).toBe('password')
  })

  it('renders submit button', async () => {
    const wrapper = await mountSuspended(LoginPage)

    const button = wrapper.find('button[type="submit"]')
    expect(button.exists()).toBe(true)
  })

  it('renders form element', async () => {
    const wrapper = await mountSuspended(LoginPage)

    expect(wrapper.find('form').exists()).toBe(true)
  })

  it('renders app title', async () => {
    const wrapper = await mountSuspended(LoginPage)

    expect(wrapper.text()).toContain('Trip App')
  })

  it('renders email and password labels', async () => {
    const wrapper = await mountSuspended(LoginPage)

    expect(wrapper.find('label[for="email"]').exists()).toBe(true)
    expect(wrapper.find('label[for="password"]').exists()).toBe(true)
  })
})

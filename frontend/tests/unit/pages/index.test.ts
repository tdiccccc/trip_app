import { describe, it, expect, vi } from 'vitest'
import { mountSuspended } from '@nuxt/test-utils/runtime'
import IndexPage from '~/pages/index.vue'

vi.stubGlobal('navigateTo', vi.fn())

describe('index page', () => {
  it('renders the app title', async () => {
    const wrapper = await mountSuspended(IndexPage)

    expect(wrapper.text()).toContain('Ise Trip')
  })

  it('renders the trip date', async () => {
    const wrapper = await mountSuspended(IndexPage)

    expect(wrapper.text()).toContain('2026.03.28')
  })

  it('renders countdown section', async () => {
    const wrapper = await mountSuspended(IndexPage)

    // Should contain at least one of the countdown labels
    const text = wrapper.text()
    const hasCountdown = text.includes('days') || text.includes('Have a great trip')
    expect(hasCountdown).toBe(true)
  })

  it('renders quick links', async () => {
    const wrapper = await mountSuspended(IndexPage)

    const text = wrapper.text()
    expect(text).toContain('しおり')
    expect(text).toContain('アルバム')
  })
})

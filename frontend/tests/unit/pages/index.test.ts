import { describe, it, expect } from 'vitest'
import { mountSuspended } from '@nuxt/test-utils/runtime'
import IndexPage from '~/pages/index.vue'

describe('index page', () => {
  it('renders an empty placeholder for redirect', async () => {
    const wrapper = await mountSuspended(IndexPage)

    // The index page is a redirect to /trips, so it renders only an empty div
    expect(wrapper.find('div').exists()).toBe(true)
    expect(wrapper.text()).toBe('')
  })
})

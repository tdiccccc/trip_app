import { describe, it, expect } from 'vitest'
import { mountSuspended } from '@nuxt/test-utils/runtime'
import TransportIcon from '~/components/TransportIcon.vue'

describe('TransportIcon', () => {
  it('renders train icon and label', async () => {
    const wrapper = await mountSuspended(TransportIcon, {
      props: { transport: 'train' },
    })

    expect(wrapper.text()).toContain('電車')
  })

  it('renders car icon and label', async () => {
    const wrapper = await mountSuspended(TransportIcon, {
      props: { transport: 'car' },
    })

    expect(wrapper.text()).toContain('車')
  })

  it('renders walk icon and label', async () => {
    const wrapper = await mountSuspended(TransportIcon, {
      props: { transport: 'walk' },
    })

    expect(wrapper.text()).toContain('徒歩')
  })

  it('renders bus icon and label', async () => {
    const wrapper = await mountSuspended(TransportIcon, {
      props: { transport: 'bus' },
    })

    expect(wrapper.text()).toContain('バス')
  })

  it('renders none icon and label', async () => {
    const wrapper = await mountSuspended(TransportIcon, {
      props: { transport: 'none' },
    })

    expect(wrapper.text()).toContain('なし')
  })

  it('renders nothing for null transport', async () => {
    const wrapper = await mountSuspended(TransportIcon, {
      props: { transport: null },
    })

    expect(wrapper.find('span').exists()).toBe(false)
  })

  it('renders nothing for unknown transport type', async () => {
    const wrapper = await mountSuspended(TransportIcon, {
      props: { transport: 'helicopter' },
    })

    // v-if="icon" should hide since unknown type returns empty string
    expect(wrapper.text()).toBe('')
  })
})

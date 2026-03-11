import { describe, it, expect } from 'vitest'
import { mountSuspended } from '@nuxt/test-utils/runtime'
import SpotCard from '~/components/SpotCard.vue'
import type { Spot } from '~/types/spot'

const baseSpot: Spot = {
  id: 1,
  trip_id: 1,
  name: 'Ise Jingu',
  description: 'Famous shrine',
  address: 'Ise, Mie Prefecture',
  latitude: 34.4551,
  longitude: 136.7259,
  business_hours: '5:00-18:00',
  price_info: 'Free',
  google_maps_url: null,
  image_url: 'https://example.com/ise.jpg',
  category: 'sightseeing',
  sort_order: 1,
  created_at: '2026-03-01T00:00:00Z',
  updated_at: '2026-03-01T00:00:00Z',
}

describe('SpotCard', () => {
  it('renders spot name', async () => {
    const wrapper = await mountSuspended(SpotCard, {
      props: { spot: baseSpot },
    })

    expect(wrapper.text()).toContain('Ise Jingu')
  })

  it('renders spot address', async () => {
    const wrapper = await mountSuspended(SpotCard, {
      props: { spot: baseSpot },
    })

    expect(wrapper.text()).toContain('Ise, Mie Prefecture')
  })

  it('renders category badge for sightseeing', async () => {
    const wrapper = await mountSuspended(SpotCard, {
      props: { spot: baseSpot },
    })

    expect(wrapper.text()).toContain('観光')
  })

  it('renders category badge for food', async () => {
    const wrapper = await mountSuspended(SpotCard, {
      props: { spot: { ...baseSpot, category: 'food' as const } },
    })

    expect(wrapper.text()).toContain('グルメ')
  })

  it('renders category badge for hotel', async () => {
    const wrapper = await mountSuspended(SpotCard, {
      props: { spot: { ...baseSpot, category: 'hotel' as const } },
    })

    expect(wrapper.text()).toContain('宿泊')
  })

  it('renders image when image_url is provided', async () => {
    const wrapper = await mountSuspended(SpotCard, {
      props: { spot: baseSpot },
    })

    const img = wrapper.find('img')
    expect(img.exists()).toBe(true)
    expect(img.attributes('src')).toBe('https://example.com/ise.jpg')
    expect(img.attributes('alt')).toBe('Ise Jingu')
  })

  it('renders placeholder when image_url is null', async () => {
    const wrapper = await mountSuspended(SpotCard, {
      props: { spot: { ...baseSpot, image_url: null } },
    })

    const img = wrapper.find('img')
    expect(img.exists()).toBe(false)
    // SVG placeholder should exist
    expect(wrapper.find('svg').exists()).toBe(true)
  })

  it('does not render address when null', async () => {
    const wrapper = await mountSuspended(SpotCard, {
      props: { spot: { ...baseSpot, address: '' } },
    })

    // Address paragraph should not be visible (v-if="spot.address")
    expect(wrapper.text()).not.toContain('Ise, Mie Prefecture')
  })

  it('links to spot detail page', async () => {
    const wrapper = await mountSuspended(SpotCard, {
      props: { spot: baseSpot },
    })

    const link = wrapper.find('a')
    expect(link.attributes('href')).toBe('/spots/1')
  })
})

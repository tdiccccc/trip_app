import { describe, it, expect } from 'vitest'
import { mountSuspended } from '@nuxt/test-utils/runtime'
import PhotoGrid from '~/components/PhotoGrid.vue'
import type { Photo } from '~/types/photo'

const createPhoto = (id: number, overrides: Partial<Photo> = {}): Photo => ({
  id,
  user_id: 1,
  spot_id: null,
  storage_path: `/photos/${id}.jpg`,
  thumbnail_path: null,
  original_filename: `photo_${id}.jpg`,
  mime_type: 'image/jpeg',
  file_size: 1024,
  caption: null,
  taken_at: null,
  created_at: '2026-03-28T00:00:00Z',
  updated_at: '2026-03-28T00:00:00Z',
  ...overrides,
})

describe('PhotoGrid', () => {
  it('renders photos in a grid', async () => {
    const photos = [createPhoto(1), createPhoto(2), createPhoto(3)]
    const wrapper = await mountSuspended(PhotoGrid, {
      props: { photos },
    })

    const images = wrapper.findAll('img')
    expect(images).toHaveLength(3)
  })

  it('uses thumbnail_path when available', async () => {
    const photos = [createPhoto(1, { thumbnail_path: '/thumbs/1.jpg' })]
    const wrapper = await mountSuspended(PhotoGrid, {
      props: { photos },
    })

    const img = wrapper.find('img')
    expect(img.attributes('src')).toBe('/thumbs/1.jpg')
  })

  it('falls back to storage_path when no thumbnail', async () => {
    const photos = [createPhoto(1)]
    const wrapper = await mountSuspended(PhotoGrid, {
      props: { photos },
    })

    const img = wrapper.find('img')
    expect(img.attributes('src')).toBe('/photos/1.jpg')
  })

  it('uses caption as alt text when available', async () => {
    const photos = [createPhoto(1, { caption: 'Beautiful shrine' })]
    const wrapper = await mountSuspended(PhotoGrid, {
      props: { photos },
    })

    const img = wrapper.find('img')
    expect(img.attributes('alt')).toBe('Beautiful shrine')
  })

  it('falls back to original_filename for alt text', async () => {
    const photos = [createPhoto(1)]
    const wrapper = await mountSuspended(PhotoGrid, {
      props: { photos },
    })

    const img = wrapper.find('img')
    expect(img.attributes('alt')).toBe('photo_1.jpg')
  })

  it('emits select event on photo click', async () => {
    const photos = [createPhoto(1), createPhoto(2)]
    const wrapper = await mountSuspended(PhotoGrid, {
      props: { photos },
    })

    const buttons = wrapper.findAll('button')
    await buttons[1].trigger('click')

    expect(wrapper.emitted('select')).toBeTruthy()
    expect(wrapper.emitted('select')![0]).toEqual([photos[1]])
  })

  it('shows empty message when no photos', async () => {
    const wrapper = await mountSuspended(PhotoGrid, {
      props: { photos: [] },
    })

    expect(wrapper.text()).toContain('No photos yet')
    expect(wrapper.findAll('img')).toHaveLength(0)
  })

  it('has 3-column grid layout', async () => {
    const photos = [createPhoto(1)]
    const wrapper = await mountSuspended(PhotoGrid, {
      props: { photos },
    })

    const grid = wrapper.find('.grid')
    expect(grid.classes()).toContain('grid-cols-3')
  })
})

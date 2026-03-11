import { describe, it, expect, vi, beforeEach, afterEach } from 'vitest'
import type { Photo } from '~/types/photo'
import { useSlideshow } from '~/composables/useSlideshow'

const createPhoto = (id: number): Photo => ({
  id,
  user_id: 1,
  spot_id: null,
  storage_path: `/photos/${id}.jpg`,
  thumbnail_path: null,
  original_filename: `${id}.jpg`,
  mime_type: 'image/jpeg',
  file_size: 1024,
  caption: null,
  taken_at: null,
  created_at: '2026-03-28T00:00:00Z',
  updated_at: '2026-03-28T00:00:00Z',
})

describe('useSlideshow', () => {
  beforeEach(() => {
    vi.useFakeTimers()
  })

  afterEach(() => {
    vi.useRealTimers()
  })

  it('initializes with correct default state', () => {
    const photos = ref<Photo[]>([createPhoto(1), createPhoto(2)])
    const slideshow = useSlideshow(photos)

    expect(slideshow.currentIndex.value).toBe(0)
    expect(slideshow.isPlaying.value).toBe(false)
    expect(slideshow.intervalMs.value).toBe(4000)
    expect(slideshow.transition.value).toBe('fade')
    expect(slideshow.totalCount.value).toBe(2)
  })

  it('currentPhoto returns the photo at currentIndex', () => {
    const photos = ref<Photo[]>([createPhoto(1), createPhoto(2)])
    const slideshow = useSlideshow(photos)

    expect(slideshow.currentPhoto.value?.id).toBe(1)
    slideshow.next()
    expect(slideshow.currentPhoto.value?.id).toBe(2)
  })

  it('currentPhoto returns null for empty list', () => {
    const photos = ref<Photo[]>([])
    const slideshow = useSlideshow(photos)

    expect(slideshow.currentPhoto.value).toBeNull()
  })

  it('next advances index when hasNext is true', () => {
    const photos = ref<Photo[]>([createPhoto(1), createPhoto(2), createPhoto(3)])
    const slideshow = useSlideshow(photos)

    slideshow.next()
    expect(slideshow.currentIndex.value).toBe(1)
    expect(slideshow.hasNext.value).toBe(true)

    slideshow.next()
    expect(slideshow.currentIndex.value).toBe(2)
    expect(slideshow.hasNext.value).toBe(false)
  })

  it('next stops playback at the end', () => {
    const photos = ref<Photo[]>([createPhoto(1), createPhoto(2)])
    const slideshow = useSlideshow(photos)

    slideshow.play()
    expect(slideshow.isPlaying.value).toBe(true)

    slideshow.next() // index 1 (last)
    slideshow.next() // should stop

    expect(slideshow.isPlaying.value).toBe(false)
    expect(slideshow.currentIndex.value).toBe(0) // stop resets to 0
  })

  it('prev decrements index when hasPrev is true', () => {
    const photos = ref<Photo[]>([createPhoto(1), createPhoto(2), createPhoto(3)])
    const slideshow = useSlideshow(photos)

    slideshow.goTo(2)
    expect(slideshow.hasPrev.value).toBe(true)

    slideshow.prev()
    expect(slideshow.currentIndex.value).toBe(1)

    slideshow.prev()
    expect(slideshow.currentIndex.value).toBe(0)
    expect(slideshow.hasPrev.value).toBe(false)
  })

  it('prev does nothing at index 0', () => {
    const photos = ref<Photo[]>([createPhoto(1), createPhoto(2)])
    const slideshow = useSlideshow(photos)

    slideshow.prev()
    expect(slideshow.currentIndex.value).toBe(0)
  })

  it('goTo sets currentIndex within bounds', () => {
    const photos = ref<Photo[]>([createPhoto(1), createPhoto(2), createPhoto(3)])
    const slideshow = useSlideshow(photos)

    slideshow.goTo(2)
    expect(slideshow.currentIndex.value).toBe(2)

    slideshow.goTo(-1)
    expect(slideshow.currentIndex.value).toBe(2)

    slideshow.goTo(5)
    expect(slideshow.currentIndex.value).toBe(2)
  })

  it('play sets isPlaying to true', () => {
    const photos = ref<Photo[]>([createPhoto(1), createPhoto(2)])
    const slideshow = useSlideshow(photos)

    slideshow.play()
    expect(slideshow.isPlaying.value).toBe(true)
  })

  it('play does nothing with empty photos', () => {
    const photos = ref<Photo[]>([])
    const slideshow = useSlideshow(photos)

    slideshow.play()
    expect(slideshow.isPlaying.value).toBe(false)
  })

  it('play auto-advances via interval', () => {
    const photos = ref<Photo[]>([createPhoto(1), createPhoto(2), createPhoto(3)])
    const slideshow = useSlideshow(photos)

    slideshow.play()
    expect(slideshow.currentIndex.value).toBe(0)

    vi.advanceTimersByTime(4000)
    expect(slideshow.currentIndex.value).toBe(1)

    vi.advanceTimersByTime(4000)
    expect(slideshow.currentIndex.value).toBe(2)
  })

  it('pause stops playback', () => {
    const photos = ref<Photo[]>([createPhoto(1), createPhoto(2), createPhoto(3)])
    const slideshow = useSlideshow(photos)

    slideshow.play()
    vi.advanceTimersByTime(4000)
    expect(slideshow.currentIndex.value).toBe(1)

    slideshow.pause()
    expect(slideshow.isPlaying.value).toBe(false)

    vi.advanceTimersByTime(4000)
    expect(slideshow.currentIndex.value).toBe(1)
  })

  it('stop resets to index 0 and pauses', () => {
    const photos = ref<Photo[]>([createPhoto(1), createPhoto(2), createPhoto(3)])
    const slideshow = useSlideshow(photos)

    slideshow.play()
    vi.advanceTimersByTime(8000)
    slideshow.stop()

    expect(slideshow.isPlaying.value).toBe(false)
    expect(slideshow.currentIndex.value).toBe(0)
  })

  it('setSpeed changes interval and restarts if playing', () => {
    const photos = ref<Photo[]>([createPhoto(1), createPhoto(2), createPhoto(3)])
    const slideshow = useSlideshow(photos)

    slideshow.play()
    slideshow.setSpeed(2000)

    expect(slideshow.intervalMs.value).toBe(2000)
    expect(slideshow.isPlaying.value).toBe(true)

    vi.advanceTimersByTime(2000)
    expect(slideshow.currentIndex.value).toBe(1)
  })

  it('setTransition changes transition type', () => {
    const photos = ref<Photo[]>([createPhoto(1)])
    const slideshow = useSlideshow(photos)

    slideshow.setTransition('slide')
    expect(slideshow.transition.value).toBe('slide')

    slideshow.setTransition('fade')
    expect(slideshow.transition.value).toBe('fade')
  })
})

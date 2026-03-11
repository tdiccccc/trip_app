import type { Photo } from './photo'

export interface Spot {
  id: number
  name: string
  description: string | null
  address: string
  latitude: number | null
  longitude: number | null
  business_hours: string | null
  price_info: string | null
  google_maps_url: string | null
  image_url: string | null
  category: 'sightseeing' | 'food' | 'hotel' | 'other'
  sort_order: number
  created_at: string
  updated_at: string
}

export interface SpotMemo {
  id: number
  spot_id: number
  user_id: number
  body: string
  created_at: string
  updated_at: string
}

export interface SpotDetail extends Spot {
  memos: SpotMemo[]
  photos: Photo[]
}

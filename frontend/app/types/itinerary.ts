export type TransportType = 'train' | 'car' | 'walk' | 'bus' | 'taxi' | 'none'

export interface ItineraryItem {
  id: number
  user_id: number
  spot_id: number | null
  title: string
  memo: string | null
  date: string
  start_time: string | null
  end_time: string | null
  transport: TransportType | null
  sort_order: number
  created_at: string
  updated_at: string
}

export interface CreateItineraryItemInput {
  spot_id?: number | null
  title: string
  memo?: string | null
  date: string
  start_time?: string | null
  end_time?: string | null
  transport?: TransportType | null
  sort_order?: number | null
}

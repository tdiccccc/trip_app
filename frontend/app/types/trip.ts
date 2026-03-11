export interface TripMember {
  id: number
  name: string
  role: 'owner' | 'member'
}

export interface Trip {
  id: number
  title: string
  description: string | null
  destination: string | null
  start_date: string
  end_date: string
  cover_image_url: string | null
  current_user_role: string | null
  members: TripMember[]
  created_at: string
  updated_at: string
}

export interface CreateTripRequest {
  title: string
  description?: string | null
  destination?: string | null
  start_date: string
  end_date: string
  member_ids?: number[]
}

export interface UpdateTripRequest {
  title?: string
  description?: string | null
  destination?: string | null
  start_date?: string
  end_date?: string
  cover_image_url?: string | null
}

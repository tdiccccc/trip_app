export interface Photo {
  id: number
  user_id: number
  spot_id: number | null
  storage_path: string
  thumbnail_path: string | null
  original_filename: string
  mime_type: string
  file_size: number
  caption: string | null
  taken_at: string | null
  created_at: string
  updated_at: string
}

export interface PaginationMeta {
  current_page: number
  last_page: number
  per_page: number
  total: number
}

export interface PaginationLinks {
  first: string | null
  last: string | null
  prev: string | null
  next: string | null
}

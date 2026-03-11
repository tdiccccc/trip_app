import type { Photo, PaginationMeta, PaginationLinks } from '~/types/photo'
import type { ApiResponse } from '~/types/auth'

interface PhotoListResponse {
  data: Photo[]
  meta: PaginationMeta
  links: PaginationLinks
}

interface FetchPhotosParams {
  spot_id?: number
  sort?: string
  order?: string
  page?: number
  per_page?: number
}

export const useAlbum = () => {
  const { apiFetch } = useApiClient()

  const fetchPhotos = (params?: FetchPhotosParams) => {
    const query = new URLSearchParams()
    if (params?.spot_id) query.set('spot_id', String(params.spot_id))
    if (params?.sort) query.set('sort', params.sort)
    if (params?.order) query.set('order', params.order)
    if (params?.page) query.set('page', String(params.page))
    if (params?.per_page) query.set('per_page', String(params.per_page))
    const qs = query.toString()
    return useApiFetch<PhotoListResponse>(`/api/photos${qs ? '?' + qs : ''}`)
  }

  const uploadPhoto = async (
    file: File,
    options?: { spot_id?: number; caption?: string; taken_at?: string },
  ) => {
    const formData = new FormData()
    formData.append('photo', file)
    if (options?.spot_id) formData.append('spot_id', String(options.spot_id))
    if (options?.caption) formData.append('caption', options.caption)
    if (options?.taken_at) formData.append('taken_at', options.taken_at)

    return apiFetch<ApiResponse<Photo>>('/api/photos', {
      method: 'POST',
      body: formData,
    })
  }

  const deletePhoto = async (id: number) => {
    return apiFetch(`/api/photos/${id}`, {
      method: 'DELETE',
    })
  }

  return { fetchPhotos, uploadPhoto, deletePhoto }
}

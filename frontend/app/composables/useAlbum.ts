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

  const fetchPhotos = (params?: MaybeRefOrGetter<FetchPhotosParams | undefined>) => {
    const url = computed(() => {
      const p = toValue(params)
      const query = new URLSearchParams()
      if (p?.spot_id) query.set('spot_id', String(p.spot_id))
      if (p?.sort) query.set('sort', p.sort)
      if (p?.order) query.set('order', p.order)
      if (p?.page) query.set('page', String(p.page))
      if (p?.per_page) query.set('per_page', String(p.per_page))
      const qs = query.toString()
      return `/api/photos${qs ? '?' + qs : ''}`
    })
    return useApiFetch<PhotoListResponse>(url)
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

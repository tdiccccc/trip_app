export interface GeolocationCoords {
  latitude: number
  longitude: number
  accuracy: number
}

export const useGeolocation = () => {
  const coords = ref<GeolocationCoords | null>(null)
  const error = ref<string | null>(null)
  const isSupported = ref(false)
  const isLoading = ref(true)
  const lastUpdated = ref<Date | null>(null)

  let watchId: number | null = null

  const start = () => {
    if (!navigator.geolocation) {
      isSupported.value = false
      isLoading.value = false
      error.value = 'お使いのブラウザは位置情報に対応していません'
      return
    }

    isSupported.value = true

    watchId = navigator.geolocation.watchPosition(
      (position) => {
        coords.value = {
          latitude: position.coords.latitude,
          longitude: position.coords.longitude,
          accuracy: position.coords.accuracy,
        }
        error.value = null
        isLoading.value = false
        lastUpdated.value = new Date()
      },
      (err) => {
        isLoading.value = false
        switch (err.code) {
          case err.PERMISSION_DENIED:
            error.value = '位置情報の利用を許可してください'
            break
          case err.POSITION_UNAVAILABLE:
            error.value = '位置情報を取得できませんでした'
            break
          case err.TIMEOUT:
            error.value = '位置情報の取得がタイムアウトしました'
            break
          default:
            error.value = '位置情報の取得に失敗しました'
        }
      },
      {
        enableHighAccuracy: false,
        maximumAge: 30000,
        timeout: 10000,
      },
    )
  }

  const stop = () => {
    if (watchId !== null) {
      navigator.geolocation.clearWatch(watchId)
      watchId = null
    }
  }

  const handleVisibilityChange = () => {
    if (document.hidden) {
      stop()
    } else {
      start()
    }
  }

  onMounted(() => {
    start()
    document.addEventListener('visibilitychange', handleVisibilityChange)
  })

  onUnmounted(() => {
    stop()
    document.removeEventListener('visibilitychange', handleVisibilityChange)
  })

  return {
    coords,
    error,
    isSupported,
    isLoading,
    lastUpdated,
  }
}

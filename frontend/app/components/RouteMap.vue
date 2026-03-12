<script setup lang="ts">
import type { Spot } from '~/types/spot'
import type { ItineraryItem } from '~/types/itinerary'

const props = defineProps<{
  spots: Spot[]
  itineraryItems: ItineraryItem[]
}>()

const mapContainer = ref<HTMLDivElement | null>(null)
const mapInstance = ref<unknown>(null)

interface MapSpot {
  order: number
  spot: Spot
  item: ItineraryItem
}

const mapSpots = computed<MapSpot[]>(() => {
  const sorted = [...props.itineraryItems].sort((a, b) => a.sort_order - b.sort_order)
  const result: MapSpot[] = []
  let order = 1

  for (const item of sorted) {
    if (!item.spot_id) continue
    const spot = props.spots.find(s => s.id === item.spot_id)
    if (!spot || spot.latitude === null || spot.longitude === null) continue
    result.push({ order: order++, spot, item })
  }

  return result
})

const hasSpots = computed(() => mapSpots.value.length > 0)

const initMap = async () => {
  if (!mapContainer.value || !hasSpots.value) return

  const L = await import('leaflet')
  await import('leaflet/dist/leaflet.css')

  const map = L.map(mapContainer.value, {
    zoomControl: true,
    attributionControl: true,
  })

  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
    maxZoom: 19,
  }).addTo(map)

  const markers: [number, number][] = []

  for (const { order, spot, item } of mapSpots.value) {
    const lat = spot.latitude as number
    const lng = spot.longitude as number
    markers.push([lat, lng])

    const icon = L.divIcon({
      className: 'route-map-marker',
      html: `<div class="route-map-marker-inner">${order}</div>`,
      iconSize: [32, 32],
      iconAnchor: [16, 16],
    })

    const timeStr = item.start_time ? item.start_time.slice(0, 5) : ''
    const popupContent = `
      <div style="font-size: 14px; line-height: 1.4;">
        <strong>${spot.name}</strong>
        ${timeStr ? `<br><span style="color: #6b7280;">${timeStr}</span>` : ''}
      </div>
    `

    L.marker([lat, lng], { icon })
      .addTo(map)
      .bindPopup(popupContent)
  }

  if (markers.length >= 2) {
    L.polyline(markers, {
      color: '#6366f1',
      weight: 3,
      opacity: 0.7,
      dashArray: '8, 8',
    }).addTo(map)
  }

  if (markers.length > 0) {
    const bounds = L.latLngBounds(markers)
    map.fitBounds(bounds, { padding: [40, 40] })
  }

  mapInstance.value = map
}

const destroyMap = () => {
  if (mapInstance.value) {
    (mapInstance.value as { remove: () => void }).remove()
    mapInstance.value = null
  }
}

onMounted(() => {
  initMap()
})

onUnmounted(() => {
  destroyMap()
})

watch(mapSpots, () => {
  destroyMap()
  nextTick(() => {
    initMap()
  })
})
</script>

<template>
  <div v-if="hasSpots">
    <ClientOnly>
      <div
        ref="mapContainer"
        class="h-[50vh] w-full rounded-xl"
      />
    </ClientOnly>
  </div>
  <div
    v-else
    class="flex h-[50vh] items-center justify-center rounded-xl bg-gray-50"
  >
    <p class="text-sm text-gray-400">
      地図に表示できるスポットがありません
    </p>
  </div>
</template>

<style>
.route-map-marker {
  background: none;
  border: none;
}

.route-map-marker-inner {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 32px;
  height: 32px;
  border-radius: 50%;
  background-color: #6366f1;
  color: white;
  font-size: 14px;
  font-weight: 700;
  box-shadow: 0 2px 6px rgb(0 0 0 / 0.3);
  border: 2px solid white;
}
</style>

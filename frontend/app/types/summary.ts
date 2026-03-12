export interface TripSummary {
  photo_count: number
  spot_count: number
  board_post_count: number
  reaction_count: number
  itinerary_item_count: number
  total_expense: number
  expense_per_person: number
  expense_by_category: Record<string, number>
  packing_total: number
  packing_checked: number
  first_photo_at: string | null
  last_photo_at: string | null
  trip_days: number
}

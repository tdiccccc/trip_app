export interface Reaction {
  id: number
  user_id: number
  emoji: string
}

export interface BoardPost {
  id: number
  user_id: number
  user_name: string
  body: string
  created_at: string
  reactions: Reaction[]
}

export interface CreateBoardPostInput {
  body: string
}

export interface CreateReactionInput {
  emoji: string
}

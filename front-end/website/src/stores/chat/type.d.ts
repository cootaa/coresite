declare interface IMessageUser {
  nickname: string
  avatar: string
}

declare interface IMessageItem {
  id?: number
  room_id?: number
  create_time: string
  message: string
  project_id?: number
  user: IMessageUser
  user_id: number
}

declare type IMessageEvents = {
  receive: {
    msg: string
    room_id: number
  }
}

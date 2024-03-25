declare interface INoticeItem {
  id: number
  group_id: number
  creator_id: number
  content: string
  status: number
  create_time: string
  update_time: string
  delete_time: null
}

declare interface IDiscussionUser {
  nickname: string
  avatar: string
}

declare interface IDiscussionItem {
  id: number
  group_id: number
  creator_id: number
  title: string
  status: number
  create_time: string
  update_time: string
  creator: IDiscussionUser
  comment_user: IDiscussionUser[]
  comment_count: number
  user_count: number
}

declare interface ICommentItem {
  id: number
  discussion_id: number
  user_id: number
  comment: string
  status: number
  create_time: string
  delete_time: string
  comment_user: IDiscussionUser
}

import type { ETheme } from '@/hooks/theme'

export interface IUserSetting {
  theme: ETheme
  lang: string
  frame: number
  side: string
}

export interface IUserState {
  user_id: number
  email: string
  nickname: string
  avatar: string
  bio: string
  setting: Partial<IUserSetting>
}

export interface IUserPublicInfo {
  nickname: string
  avatar: string
  bio: string
}

export interface ICreator {
  nickname: string // 昵称
  avatar: string // 头像
}

import type { IUserSetting } from '@/stores/user/type'
import type { HttpResponse } from '@/utils/interceptor'
import axios from 'axios'

export interface IGetProfileData {
  user_id: number
}

export interface IGetProfileResponse {
  avatar: string
  bio: string
  create_time: string
  id: number
  limited: number
  nickname: string
  username: string
  setting: IUserSetting
}

export interface IUpdateProfileData {
  user_id: number
  nickname?: string
  avatar?: string
  bio?: string
  lang?: string
  theme?: string
  frame?: number
  side?: string
}

export function getProfile(formData: IGetProfileData) {
  return axios.get<HttpResponse<IGetProfileResponse>>('/user/info', {
    params: formData
  })
}

export function updateProfile(formData: IUpdateProfileData) {
  return axios.post<HttpResponse<IGetProfileResponse>>('/user/update', formData)
}

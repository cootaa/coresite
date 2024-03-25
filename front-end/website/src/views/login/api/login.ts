import type { IUserSetting } from '@/stores/user/type'
import type { HttpResponse } from '@/utils/interceptor'
import axios from 'axios'

export interface ILoginData {
  username: string
  password: string
}

export interface ILoginResponse {
  token: string
  expire_time: number
  nickname: string
  avatar: string
  setting: IUserSetting
}

export const login = (data: ILoginData) => {
  return axios.post<HttpResponse<ILoginResponse>>('/user/login', data)
}

export const logout = () => {
  return axios.post<HttpResponse<unknown>>('/user/logout')
}

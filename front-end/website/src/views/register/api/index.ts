import type { HttpResponse } from '@/utils/interceptor'
import axios from 'axios'

export interface IRegisterData {
  username: string
  password: string
  nickname: string
}

export type TRegisterResponse = string

export function register(data: IRegisterData) {
  return axios.post<HttpResponse<TRegisterResponse>>('/user/register', data)
}

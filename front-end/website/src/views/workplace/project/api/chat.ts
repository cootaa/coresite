import type { HttpResponse } from '@/utils/interceptor'
import axios from 'axios'

/**
 * 获取聊天列表表单数据类型
 */
export interface IGetChatListData {
  page: number
  size: number
  project_id: number
  nickname?: string
  message?: string
  user_id: number
}

export interface IGetChatListResponse {
  count: number
  page: number
  size: number
  chat_list: IMessageItem[]
}

/**
 * 获取聊天列表
 * @param formData
 * @returns
 */
export function getChatList(formData: IGetChatListData) {
  return axios.get<HttpResponse<IGetChatListResponse>>('/chat/list', { params: formData })
}

export interface ISendMessageData {
  message: string
  user_id: number
  project_id: number
}

export function sendMessage(formData: ISendMessageData) {
  return axios.post<HttpResponse<unknown>>('/chat/save', formData)
}

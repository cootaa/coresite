import type { HttpResponse } from '@/utils/interceptor'
import axios from 'axios'

/**
 * 获取公告列表表单数据
 */
export interface IGetNoticesData {
  group_id: number // 组织 ID
  user_id: number // 用户 ID
  page?: number // 页码
  size?: number // 每页数量
}

/**
 * 获取公告列表返回数据类型
 */
export interface IGetNoticesResponse {
  page: number // 页码
  size: number // 每页数量
  notice_list: INoticeItem[] // 通知列表
}

/**
 * 获取公告列表
 * @param formData
 * @returns
 */
export function getNotices(formData: IGetNoticesData) {
  return axios.get<HttpResponse<IGetNoticesResponse>>('/notice/list', { params: formData })
}

/**
 * 创建公告表单数据类型
 */
export interface ICreateNoticeData {
  creator_id: number // 公告创建者 ID
  group_id: number // 组织 ID
  content: string // 公告内容
}

/**
 * 创建公告
 * @param formData
 * @returns
 */
export function createNotice(formData: ICreateNoticeData) {
  return axios.post<HttpResponse<unknown>>('/notice/create', formData)
}

/**
 * 删除公告表单数据类型
 */
export interface IDeleteNoticeData {
  group_id: number // 组织 ID
  notice_id: number // 公告 ID
  creator_id: number // 公告创建者 ID
}

/**
 * 删除公告
 * @param formData
 * @returns
 */
export function deleteNotice(formData: IDeleteNoticeData) {
  return axios.post<HttpResponse<string>>('/notice/del', formData)
}

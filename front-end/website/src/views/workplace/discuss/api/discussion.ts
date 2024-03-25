import type { HttpResponse } from '@/utils/interceptor'
import axios from 'axios'

/**
 * 获取讨论列表表单数据类型
 */
export interface IGetDiscussionListData {
  page: number // 页码
  size: number // 每页最大数量
  group_id: number // 组织 ID
  user_id: number // 用户 ID
  content?: string // 内容 （模糊搜索）
  title?: string // 标题 （模糊搜索）
}

/**
 * 获取讨论列表返回数据类型
 */
export interface IGetDiscussionListResponse {
  page: number // 页码
  size: number // 每页最大数量
  discussion_list: IDiscussionItem[]
}

/**
 * 获取讨论列表
 * @param formData 表单数据
 * @returns
 */
export function getDiscussionList(formData: IGetDiscussionListData) {
  return axios.get<HttpResponse<IGetDiscussionListResponse>>(`/discussion/list`, {
    params: formData
  })
}

/**
 * 创建讨论表单数据类型
 */
export interface ICreateDiscussionData {
  creator_id: number
  group_id: number
  title: string
  content: string
}

/**
 * 创建讨论
 * @param formData
 * @returns
 */
export function createDiscussion(formData: ICreateDiscussionData) {
  return axios.post<HttpResponse<unknown>>(`/discussion/create`, formData)
}

/**
 * 获取讨论详情表单数据类型
 */
export interface IGetDiscussionDetailData {
  discussion_id: number // 讨论 ID
  order?: 'desc' | 'asc' // 评论排序，默认 asc
}

/**
 * 获取讨论详情返回值类型
 */
export interface IGetDiscussionDetailResponse {
  id: number
  group_id: number
  creator_id: number
  title: string
  content: string
  status: number
  create_time: string
  update_time: string
  delete_time: string
  creator: IDiscussionUser
  comments: ICommentItem[]
}

/**
 * 获取讨论详情
 * @param formData
 * @returns
 */
export function getDiscussionDetail(formData: IGetDiscussionDetailData) {
  return axios.get<HttpResponse<IGetDiscussionDetailResponse>>(`/discussion/content`, {
    params: formData
  })
}

/**
 * 发布评论表单数据类型
 */
export interface IAddCommentData {
  group_id: number
  discussion_id: number
  user_id: number
  comment: string
}

/**
 * 发布评论
 * @param formData
 * @returns
 */
export function addComment(formData: IAddCommentData) {
  return axios.post<HttpResponse<unknown>>(`/discussion/comment/add`, formData)
}

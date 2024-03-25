import type { HttpResponse } from '@/utils/interceptor'
import axios from 'axios'

/**
 * 创建组织表单
 */
export interface ICreateGroupData {
  creator_id: number
  name: string
  icon: string
}

/**
 * 获取组织列表表单
 */
export interface IGetGroupListData {
  user_id: number
  page: number
  size: number
  group_name: string
}

/**
 * 组织创建者信息
 */
export interface IGroupCreator {
  nickname: string
  avatar: string
}

/**
 * 组织信息
 */
export interface IGroupItem {
  creator_id: number
  group_id: number
  icon: string
  name: string
  status: number
  creator: IGroupCreator
  members: IGroupCreator[]
}

/**
 * 获取组织列表相应内容
 */
export interface IGetGroupListResponse {
  count: number
  page: number
  size: number
  joined_group: IGroupItem[]
}

/**
 * 创建组织相应内容
 */
export type TCreateGroupResponse = string

/**
 * 创建组织
 * @param {ICreateGroupData} formData
 * @returns
 */
export function createGroup(formData: ICreateGroupData) {
  return axios.post<HttpResponse<TCreateGroupResponse>>('/Group/create', formData)
}

/**
 * 获取组织列表
 * @param {Partial<IGetGroupListData>} formData
 * @returns
 */
export function getGroupList(formData: Partial<IGetGroupListData>) {
  return axios.get<HttpResponse<IGetGroupListResponse>>('/group/list', {
    params: formData
  })
}

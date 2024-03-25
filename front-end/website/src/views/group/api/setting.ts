import type { IUserPublicInfo } from '@/stores/user/type'
import type { HttpResponse } from '@/utils/interceptor'
import axios from 'axios'

/**
 * 更新组织信息数据
 */
export interface IUpdateGroupInfo {
  id: number // 组织 ID
  name?: string // 组织名称
  icon?: string // 组织图标
  creator_id?: number // 创建人 ID
}

/**
 * 更新组织信息
 * @param formData 表单
 * @returns
 */
export function updateGroupInfo(formData: IUpdateGroupInfo) {
  return axios.post<HttpResponse<unknown>>('/group/update', formData)
}

/**
 * 获取组织成员表单内容
 */
export interface IGetGroupMembersData {
  group_id: number // 组织 id
  page?: number
  size?: number
  nickname?: string // 用户昵称
}

/**
 * 成员信息
 */
export interface IMemberType {
  group_id: number // 组织 ID
  id: number // 成员表 ID
  user: IUserPublicInfo // 成员信息
  user_id: number // 成员 ID
}

/**
 * 获取组织成员返回值
 */
export interface IGetGroupMembersResponse {
  count: number // 成员数量
  group_list: IMemberType[] // 成员列表
  page: string
  size: string
}

/**
 * 获取组织成员列表
 * @param formData 表单
 * @returns
 */
export function getGroupMembers(formData: IGetGroupMembersData) {
  return axios.get<HttpResponse<IGetGroupMembersResponse>>('/group/member/list', {
    params: formData
  })
}

/**
 * 添加成员表单
 */
export interface IAddGroupMemberData {
  creator_id: number // 组织创建者用户 ID
  group_id: number // 组织 ID
  username: string // 被邀请用户邮箱
}

/**
 * 添加成员
 * @param formData 表单信息
 * @returns
 */
export function addGroupMember(formData: IAddGroupMemberData) {
  return axios.post<HttpResponse<string>>('/group/add', formData)
}

/**
 * 删除成员表单
 */
export interface IDeleteGroupMemberData {
  creator_id: number
  member_ids: number[] // 成员 id 数组
  group_id: number
}

/**
 * 删除指定成员
 * @param formData 表单信息
 * @returns
 */
export function deleteGroupMember(formData: IDeleteGroupMemberData) {
  return axios.post<HttpResponse<string>>('group/member/del', formData)
}

/**
 * 开启或关闭组织表单数据类型
 */
export interface IUpdateGroupStatusData {
  group_id: number // 组织 ID
  creator_id: number // 组织创建者用户 ID
}

/**
 * 开启或关闭组织
 * @param formData 表单数据
 * @returns
 */
export function updateGroupStatus(formData: IUpdateGroupStatusData) {
  return axios.post<HttpResponse<string>>('/group/dismiss', formData)
}

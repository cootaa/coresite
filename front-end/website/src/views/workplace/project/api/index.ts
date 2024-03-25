import type { HttpResponse } from '@/utils/interceptor'
import type { IMemberType } from '@/views/group/api/setting'
import axios from 'axios'

export interface IGetProjectListData {
  user_id: number
  group_id: number
  name?: string
  page?: number
  size?: number
}

export type TGetProjectListResponse = IProjectItem[]

export interface IProjectCreatorInfo {
  nickname: string // 昵称
  avatar: string // 头像
}

export interface IProjectInfo {
  name: string // 项目名称
  icon: string // 项目图标
  creator_info: IProjectCreatorInfo // 项目创建者信息
}

export interface IProjectItem {
  group_id: number // 组织 ID
  project_id: number // 项目 ID
  user_id: number // 用户 ID
  status: number // 项目开启状态
  project_info: IProjectInfo // 项目信息
}

/**
 * 获取项目列表
 * @param formData 表单信息
 * @returns
 */
export function getProjectList(formData: IGetProjectListData) {
  return axios.get<HttpResponse<TGetProjectListResponse>>('/project/list', {
    params: formData
  })
}

/**
 * 新建项目参数数据
 */
export interface ICreateProjectData {
  group_id: number // 组织 ID
  name: string // 项目名称
  icon: string // 项目图标
  creator_id: number // 创建者 ID
}

/**
 * 新建项目返回值类型
 */
export interface ICreateProjectResponse {
  create_time: string // 创建时间
  creator_id: number // 创建者 ID
  group_id: number // 组织 ID
  icon: string // 项目图标
  id: number // 项目 ID
  name: string // 项目名称
  update_time: string // 项目信息更新时间
}

/**
 * 新建项目
 * @param formData 表单信息
 * @returns
 */
export function createProject(formData: ICreateProjectData) {
  return axios.post<HttpResponse<ICreateProjectResponse>>('/project/create', formData)
}

/**
 * 获取项目成员表单数据
 */
export interface IGetProjectMembersData {
  group_id: number // 组织 ID
  project_id: number // 项目 ID
}

/**
 * 获取项目成员返回值类型
 */
export interface IGetProjectMembersResponse {
  count: number
  page: string
  size: string
  project_list: IMemberType[]
}

/**
 * 获取项目成员
 * @param formData 表单数据
 * @returns
 */
export function getProjectMembers(formData: IGetProjectMembersData) {
  return axios.get<HttpResponse<IGetProjectMembersResponse>>('/project/member/list', {
    params: formData
  })
}

/**
 * 移除项目成员表单数据类型
 */
export interface IDeleteProjectMemberData {
  group_id: number // 组织 ID
  creator_id: number // 项目创建者 ID
  project_id: number // 项目 ID
  member_ids: number[] // 成员 ID列表
}

/**
 * 移除项目成员
 * @param formData 表单数据
 * @returns
 */
export function deleteProjectMember(formData: IDeleteProjectMemberData) {
  return axios.post<HttpResponse<string>>('/project/member/del', formData)
}

/**
 * 添加项目成员表单数据类型
 */
export interface IAddProjectMemberData {
  project_id: number // 项目 ID
  group_id: number // 组织 ID
  creator_id: number // 项目创建者 ID
  member_id: number // 被添加成员 ID
}

/**
 * 添加项目成员
 * @param formData 表单数据
 * @returns
 */
export function addProjectMember(formData: IAddProjectMemberData) {
  return axios.post<HttpResponse<string>>('/project/member/add', formData)
}

/**
 * 关闭开启项目表单
 */
export interface IUpdateProjectStatusData {
  project_id: number // 项目 ID
  creator_id: number // 项目创建者 ID
}

/**
 * 关闭开启项目
 */
export function updateProjectStatus(formData: IUpdateProjectStatusData) {
  return axios.post<HttpResponse<string>>('/project/dismiss', formData)
}

/**
 * 更新项目信息表单数据类型
 */
export interface IUpdateProjectData {
  creator_id: number // 项目创建者 ID
  group_id: number // 组织 ID
  project_id: number // 项目 ID
  icon?: string // 项目图标
  name?: string // 项目名称
}

/**
 * 更新项目信息
 * @param formData 表单数据
 * @returns
 */
export function updateProject(formData: IUpdateProjectData) {
  return axios.post<HttpResponse<unknown>>('/project/update', formData)
}

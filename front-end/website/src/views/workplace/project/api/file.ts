import axios from 'axios'
import type { HttpResponse } from '@/utils/interceptor'
import type { ICreator } from '@/stores/user/type'

/**
 * 文件信息
 */
export interface IFileItem {
  id: number
  folder_id: number
  name: string
  type: string
  format: string
  size: number
  url: string
  creator_id: number
  create_time: string
  update_time: string
  creator: ICreator
}

/**
 * 文件夹信息
 */
export interface IFolderItem {
  create_time: string
  creator: ICreator
  creator_id: number
  files?: IFileItem[]
  id: number
  name: string
  parent_id: number
  project_id: number
  sub_folders: IFolderItem[]
  update_time: string
}

/**
 * 获取文件列表表单信息数据类型
 */
export interface IGetFileFolderListData {
  name?: string
  project_id: number
  user_id: number
  folder_id?: number
}

/**
 * 获取文件列表返回值类型
 */
export interface IGetFileFolderListResponse {
  count: number // 文件数量
  folder_info: IFolderItem
}

/**
 * 获取文件夹 && 文件列表
 * @param formData
 * @returns
 */
export function getFileFolderList(formData: IGetFileFolderListData) {
  return axios.get<HttpResponse<IGetFileFolderListResponse>>('/folder/list', {
    params: formData
  })
}

/**
 * 创建文件夹表单数据类型
 */
export interface ICreateFolderData {
  name: string // 文件夹名称
  project_id: number // 项目 ID
  creator_id: number // 创建者 ID
  parent_id: number // 父文件夹 ID
}

/**
 * 创建文件夹
 * @param formData
 * @returns
 */
export function createFolder(formData: ICreateFolderData) {
  return axios.post<HttpResponse<unknown>>('/folder/create', formData)
}

/**
 * 上传文件请求数据类型
 */
export interface IUploadFileData {
  file: File // 文件
  group_id: number // 组织 ID
  project_id: number // 项目 ID
}

/**
 * 上传文件返回数据类型
 */
export interface IUploadFileResponse {
  url: string // 文件链接
  name: string // 文件名称
  format: string // 文件格式
  size: number // 文件大小
  type: string // 文件类型
}

/**
 * 上传文件
 * @param formData
 * @returns
 */
export function uploadFile(formData: IUploadFileData) {
  return axios.post<HttpResponse<IUploadFileResponse>>('/upload/file', formData, {
    headers: { 'Content-Type': 'multipart/form-data' }
  })
}

/**
 * 待提交文件数据类型
 */
export interface IUploadFileItem {
  folder_id: number // 文件夹 ID
  name: string // 文件名称
  type: string // 文件类型
  format: string // 文件格式
  size: number // 文件大小
  url: string // 文件链接
}

/**
 * 提交上传信息表单数据类型
 */
export interface IUploadFilesInfoData {
  creator_id: number // 上传者 ID
  project_id: number // 项目 ID
  files: IUploadFileItem[] // 文件列表
  confirm: 1 | 0 // 1 则覆盖上传 0 则不覆盖
}

/**
 * 提交上传信息：上传文件后需要调用此接口提交信息
 * @param formData
 * @returns
 */
export function uploadFilesInfo(formData: IUploadFilesInfoData) {
  return axios.post<HttpResponse<number>>('/file/upload', formData)
}

/**
 * 删除文件夹表单数据类型
 */
export interface IDeleteFolderData {
  project_id: number // 项目 ID
  folder_id: number // 文件夹 ID
  creator_id: number // 文件夹创建者 ID
}

/**
 * 删除文件夹
 */
export function deleteFolder(formData: IDeleteFolderData) {
  return axios.post<HttpResponse<string>>('/folder/del', formData)
}

/**
 * 删除文件表单数据类型
 */
export interface IDeleteFileData {
  file_id: number
  folder_id: number
  creator_id: number
}

/**
 * 删除文件
 * @param formData
 * @returns
 */
export function deleteFile(formData: IDeleteFileData) {
  return axios.post<HttpResponse<string>>('/file/del', formData)
}

/**
 * 文件夹更名表单数据类型
 */
export interface IFolderRenameData {
  name: string
  folder_id: number
  project_id: number
  creator_id: number
}

/**
 * 文件夹更名
 * @param formData
 * @returns
 */
export function folderRename(formData: IFolderRenameData) {
  return axios.post<HttpResponse<unknown>>('/folder/rename', formData)
}

/**
 * 文件更名表单数据类型
 */
export interface IFileRenameData {
  name: string
  creator_id: number
  folder_id: number
  file_id: number
}

/**
 * 文件更名
 * @param formData 表单数据
 * @returns
 */
export function fileRename(formData: IFileRenameData) {
  return axios.post<HttpResponse<unknown>>('/file/rename', formData)
}

/**
 * 获取项目目录树表单数据类型
 */
export interface IGetFolderTreeData {
  project_id: number // 项目 ID
}

/**
 * 目录树类型
 */
export interface IGetFolderTreeInfo {
  id: number
  project_id: number
  name: string
  parent_id: number
  creator_id: number
  create_time: string
  update_time: string
  sub_folders?: IGetFolderTreeInfo[]
}

/**
 * 获取目录树返回值类型
 */
export interface IGetFolderTreeResponse {
  folder_info: IGetFolderTreeInfo
}

/**
 * 获取项目目录树
 * @param formData
 * @returns
 */
export function getFolderTree(formData: IGetFolderTreeData) {
  return axios.get<HttpResponse<IGetFolderTreeResponse>>('/folder/tree', { params: formData })
}

/**
 * 移动目录表单数据类型
 */
export interface IMoveFolderData {
  folder_id: number // 被移动的目录 ID
  parent_id: number // 目标目录 ID
  creator_id: number
}

/**
 * 移动目录
 * @param formData 表单数据
 * @returns
 */
export function moveFolder(formData: IMoveFolderData) {
  return axios.post<HttpResponse<string>>('/folder/remove', formData)
}

/**
 * 移动文件表单数据类型
 */
export interface IMoveFileData {
  file_id: number // 被移动文件 ID
  folder_id: number // 目标目录 ID
  confirm?: 1 | 0 // 是否覆盖
  creator_id: number
}

/**
 * 移动文件
 * @param formData 表单数据
 * @returns
 */
export function moveFile(formData: IMoveFileData) {
  return axios.post<HttpResponse<string>>('/file/remove', formData)
}

/**
 * 下载文件表单数据类型
 */
export interface IDownloadFileData {
  user_id: number
  file_id: number
}

/**
 * 下载文件返回数据类型
 */
export interface IDownloadFileResponse {
  file_name: string
  download_url: string
}

/**
 * 下载文件
 * @param formData
 */
export function downloadFile(formData: IDownloadFileData) {
  return axios.post<HttpResponse<IDownloadFileResponse>>('/file/getFileUrl', formData)
}

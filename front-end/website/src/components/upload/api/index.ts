import type { HttpResponse } from '@/utils/interceptor'
import axios from 'axios'

export enum EUploadType {
  AVATAR = 'avatar',
  ICON = 'icon'
}

export interface IUploadData {
  type: EUploadType
  file: File
}

export interface IUploadResponse {
  url: string
}

export function uploadImage(formData: IUploadData) {
  return axios.post<HttpResponse<IUploadResponse>>('/upload/img', formData, {
    headers: { 'Content-Type': 'multipart/form-data' }
  })
}

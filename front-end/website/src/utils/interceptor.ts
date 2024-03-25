import axios from 'axios'
import type { AxiosRequestHeaders, AxiosResponse, InternalAxiosRequestConfig } from 'axios'
import { Message } from '@arco-design/web-vue'
import { getToken, clearToken } from '@/utils/token'
import { LS_LANG } from '@/config/localstorageKey'

export interface HttpResponse<T = unknown> {
  msg: string
  code: number
  data: T
}

if (import.meta.env.VITE_API_BASE_URL) {
  axios.defaults.baseURL = import.meta.env.VITE_API_BASE_URL
}

axios.interceptors.request.use(
  (config: InternalAxiosRequestConfig) => {
    if (!config.headers) {
      config.headers = {} as AxiosRequestHeaders
    }
    // 携带 token
    const token = getToken()
    if (token) {
      config.headers.Authorization = `Bearer ${token}`
      config.headers.token = `${token}`
    }
    // 携带 lang
    const lang = localStorage.getItem(LS_LANG) || 'en-US'
    config.headers.lang = lang
    return config
  },
  (error) => {
    return Promise.reject(error)
  }
)

axios.interceptors.response.use(
  (response: AxiosResponse<HttpResponse>) => {
    const res = response.data
    if (res.code !== 200) {
      // token 无效
      if (res.code === 440) {
        clearToken()
      }
      Message.error({
        content: res.msg || 'Error',
        duration: 5 * 1000
      })
      return Promise.reject(new Error(res.msg || 'Error'))
    }
    return response
  },
  (error) => {
    Message.error({
      content: error.msg || 'Request Error',
      duration: 5 * 1000
    })
    return Promise.reject(error)
  }
)

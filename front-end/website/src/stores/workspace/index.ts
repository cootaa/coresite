import {
  getNotices,
  createNotice as createNoticeAPI,
  type ICreateNoticeData,
  type IGetNoticesData,
  type IDeleteNoticeData,
  deleteNotice
} from '@/views/workplace/discuss/api/notice'
import { defineStore } from 'pinia'
import useUserStore from '../user'
import {
  getDiscussionList,
  createDiscussion as creaeteDiscussionAPI,
  type ICreateDiscussionData,
  type IGetDiscussionListData,
  type IGetDiscussionDetailData,
  getDiscussionDetail,
  type IAddCommentData,
  addComment
} from '@/views/workplace/discuss/api/discussion'

const useWorkspaceStore = defineStore('workspace', () => {
  const userStore = useUserStore()

  // 通知列表
  const getNoticeList = async (formData: Omit<IGetNoticesData, 'user_id'>) => {
    const user_id = userStore.user_id
    if (!user_id) return
    if (!formData.group_id) return
    try {
      const res = await getNotices({ ...formData, user_id })
      return res.data.data
    } catch (e) {
      throw new Error(e as string)
    }
  }

  // 创建公告
  const createNotice = async (formData: Omit<ICreateNoticeData, 'creator_id'>) => {
    const creator_id = userStore.user_id
    if (!creator_id) return
    const data: ICreateNoticeData = {
      ...formData,
      creator_id
    }
    try {
      const res = await createNoticeAPI(data)
      if (res.data.code === 200) {
        return res.data
      } else {
        throw new Error(res.data.msg)
      }
    } catch (e) {
      throw new Error(e as string)
    }
  }

  // 删除公告
  const delNotice = async (formData: Omit<IDeleteNoticeData, 'creator_id'>) => {
    const creator_id = userStore.user_id
    if (!creator_id) return
    const data: IDeleteNoticeData = {
      ...formData,
      creator_id
    }
    try {
      const res = await deleteNotice(data)
      if (res.data.code === 200) {
        return res.data
      } else {
        throw new Error(res.data.msg)
      }
    } catch (e) {
      throw new Error(e as string)
    }
  }

  // 获取讨论列表
  const getDiscussions = async (formData: Omit<IGetDiscussionListData, 'user_id'>) => {
    const user_id = userStore.user_id
    if (!user_id) return
    const data = { ...formData, user_id }
    try {
      const res = await getDiscussionList(data)
      if (res.data.code === 200) {
        return res.data.data
      } else {
        throw new Error(res.data.msg)
      }
    } catch (e) {
      throw new Error(e as string)
    }
  }

  // 发布讨论
  const createDiscussion = async (formData: Omit<ICreateDiscussionData, 'creator_id'>) => {
    const creator_id = userStore.user_id
    if (!creator_id) return
    const data = { ...formData, creator_id }
    try {
      const res = await creaeteDiscussionAPI(data)
      if (res.data.code === 200) {
        return res.data
      } else {
        throw new Error(res.data.msg)
      }
    } catch (e) {
      throw new Error(e as string)
    }
  }

  // 获取讨论列表
  const getDiscussionContent = async (formData: IGetDiscussionDetailData) => {
    try {
      const res = await getDiscussionDetail(formData)
      if (res.data.code === 200) {
        return res.data.data
      } else {
        throw new Error(res.data.msg)
      }
    } catch (e) {
      throw new Error(e as string)
    }
  }

  // 发布评论
  const createComment = async (formData: Omit<IAddCommentData, 'user_id'>) => {
    const user_id = userStore.user_id
    if (!user_id) return
    const data = { ...formData, user_id }
    try {
      const res = await addComment(data)
      if (res.data.code === 200) {
        return res.data
      } else {
        throw new Error(res.data.msg)
      }
    } catch (e) {
      throw new Error(e as string)
    }
  }

  return {
    getNoticeList,
    createNotice,
    delNotice,
    getDiscussions,
    createDiscussion,
    getDiscussionContent,
    createComment
  }
})

export default useWorkspaceStore

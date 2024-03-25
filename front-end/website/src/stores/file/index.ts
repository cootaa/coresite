import { defineStore } from 'pinia'
import useUserStore from '../user'
import useGroupStore from '../group'
import {
  createFolder as createFolderAPI,
  getFileFolderList,
  uploadFile as uploadFileAPI,
  type ICreateFolderData,
  type IGetFileFolderListData,
  type IUploadFileData,
  uploadFilesInfo,
  type IUploadFilesInfoData,
  type IUploadFileItem,
  type IDeleteFolderData,
  deleteFolder as deleteFolderAPI,
  type IDeleteFileData,
  deleteFile as deleteFileAPI,
  type IFolderRenameData,
  folderRename as folderRenameAPI,
  type IFileRenameData,
  fileRename as fileRenameAPI,
  type IGetFolderTreeData,
  getFolderTree as getFolderTreeAPI,
  type IGetFolderTreeInfo,
  type IMoveFolderData,
  moveFolder,
  type IMoveFileData,
  moveFile,
  type IDownloadFileData,
  downloadFile
} from '@/views/workplace/project/api/file'

const userStore = useUserStore()
const groupStore = useGroupStore()

const useFileStore = defineStore('file', {
  state: () => ({
    tree: {
      folder: undefined,
      status: undefined
    } as {
      folder?: IGetFolderTreeInfo
      status?: 'success' | 'error' | 'loading'
    }
  }),
  actions: {
    /**
     * 获取文件夹列表
     */
    async getFileFolders(formData: Omit<IGetFileFolderListData, 'user_id'>) {
      const user_id = userStore.user_id
      if (!user_id) return
      try {
        const res = await getFileFolderList({ ...formData, user_id })
        const { code, data, msg } = res.data
        if (code === 200) {
          return data
        } else {
          throw new Error(msg)
        }
      } catch (e) {
        throw new Error((e as Error).message)
      }
    },
    /**
     * 创建文件夹
     * @param formData
     * @returns
     */
    async createFolder(formData: Omit<ICreateFolderData, 'creator_id'>) {
      const user_id = userStore.user_id
      if (!user_id) return
      const { project_id, parent_id, name } = formData
      if (!project_id) return
      // 待传入数据
      const data: ICreateFolderData = { project_id, creator_id: user_id, name, parent_id }
      // 提交请求
      try {
        const res = await createFolderAPI(data)
        const { code, data: ret, msg } = res.data
        if (code === 200) {
          return ret
        } else {
          throw new Error(msg)
        }
      } catch (e) {
        throw new Error((e as Error).message)
      }
    },
    /**
     * 上传文件
     * @param formData
     * confirm: 1 则覆盖上传 0 则不覆盖
     * @returns
     */
    async uploadFile(
      formData: Omit<IUploadFileData, 'creator_id'> &
        Omit<IUploadFilesInfoData, 'files' | 'creator_id'> & { folder_id: number }
    ) {
      const user_id = userStore.user_id
      if (!user_id) return
      const { project_id, group_id, folder_id, file, confirm } = formData
      // 待传入数据
      const data: IUploadFileData = { project_id, group_id, file }
      // 提交请求
      try {
        // 文件上传请求
        const res = await uploadFileAPI(data)
        const { code, data: ret, msg } = res.data
        if (code === 200) {
          // 文件保存请求
          const fileItem: IUploadFileItem = {
            folder_id,
            name: ret.name,
            type: ret.type,
            format: ret.format,
            size: ret.size,
            url: ret.url
          }
          const data: IUploadFilesInfoData = {
            project_id,
            creator_id: user_id,
            files: [fileItem],
            confirm
          }
          const res = await uploadFilesInfo(data)
          const { code, msg } = res.data
          if (code === 200) {
            return res.data
          }
          throw new Error(msg)
        } else {
          throw new Error(msg)
        }
      } catch (e) {
        throw new Error((e as Error).message)
      }
    },
    /**
     * 删除文件夹
     * @param formData
     * @returns
     */
    async deleteFolder(formData: Omit<IDeleteFolderData, 'creator_id'>) {
      const user_id = userStore.user_id
      if (!user_id) return
      const { project_id, folder_id } = formData
      // 待传入数据
      const data: IDeleteFolderData = { project_id, creator_id: user_id, folder_id }
      // 提交请求
      try {
        const res = await deleteFolderAPI(data)
        const { code, data: ret, msg } = res.data
        if (code === 200) {
          return ret
        } else {
          throw new Error(msg)
        }
      } catch (e) {
        throw new Error((e as Error).message)
      }
    },
    /**
     * 删除文件
     * @param formData
     * @returns
     */
    async deleteFile(formData: Omit<IDeleteFileData, 'creator_id'>) {
      const user_id = userStore.user_id
      if (!user_id) return
      const { file_id, folder_id } = formData
      // 待传入数据
      const data: IDeleteFileData = { file_id, creator_id: user_id, folder_id }
      // 提交请求
      try {
        const res = await deleteFileAPI(data)
        const { code, data: ret, msg } = res.data
        if (code === 200) {
          return ret
        } else {
          throw new Error(msg)
        }
      } catch (e) {
        throw new Error((e as Error).message)
      }
    },
    /**
     * 文件夹更名
     * @param formData
     * @returns
     */
    async folderRename(formData: Omit<IFolderRenameData, 'creator_id'>) {
      const user_id = userStore.user_id
      if (!user_id) return
      const { name, folder_id, project_id } = formData
      // 待传入数据
      const data: IFolderRenameData = { name, folder_id, project_id, creator_id: user_id }
      // 提交请求
      try {
        const res = await folderRenameAPI(data)
        const { code, msg } = res.data
        if (code === 200) {
          return res
        } else {
          throw new Error(msg)
        }
      } catch (e) {
        throw new Error((e as Error).message)
      }
    },
    /**
     * 文件更名
     * @param formData
     * @returns
     */
    async fileRename(formData: Omit<IFileRenameData, 'creator_id'>) {
      const user_id = userStore.user_id
      if (!user_id) return
      const { name, folder_id, file_id } = formData
      // 待传入数据
      const data: IFileRenameData = { name, folder_id, file_id, creator_id: user_id }
      // 提交请求
      try {
        const res = await fileRenameAPI(data)
        const { code, msg } = res.data
        if (code === 200) {
          return res
        } else {
          throw new Error(msg)
        }
      } catch (e) {
        throw new Error((e as Error).message)
      }
    },
    /**
     * 获取目录树
     */
    async getFolderTree(formData: IGetFolderTreeData) {
      this.tree.status = 'loading'
      try {
        const res = await getFolderTreeAPI(formData)
        const { code, data, msg } = res.data
        if (code === 200) {
          this.tree.folder = data.folder_info
          this.tree.status = 'success'
        } else {
          this.tree.status = 'error'
          throw new Error(msg)
        }
      } catch (e) {
        this.tree.status = 'error'
        throw new Error((e as Error).message)
      }
    },
    /**
     * 移动目录
     * @param formData
     * @returns
     */
    async removeFolder(formData: Omit<IMoveFolderData, 'creator_id'>) {
      const user_id = userStore.user_id
      if (!user_id) return
      const { folder_id, parent_id } = formData
      // 待传入数据
      const data: IMoveFolderData = { folder_id, parent_id, creator_id: user_id }
      // 提交请求
      try {
        const res = await moveFolder(data)
        const { code, msg } = res.data
        if (code === 200) {
          return res.data
        } else {
          throw new Error(msg)
        }
      } catch (e) {
        throw new Error((e as Error).message)
      }
    },
    /**
     * 移动文件
     * @param formData
     * @returns
     */
    async removeFile(formData: Omit<IMoveFileData, 'creator_id'>) {
      const user_id = userStore.user_id
      if (!user_id) return
      const { folder_id, file_id, confirm } = formData
      // 待传入数据
      const data: IMoveFileData = { folder_id, file_id, confirm, creator_id: user_id }
      // 提交请求
      try {
        const res = await moveFile(data)
        const { code, msg } = res.data
        if (code === 200) {
          return res.data
        } else {
          throw new Error(msg)
        }
      } catch (e) {
        throw new Error((e as Error).message)
      }
    },
    /**
     *
     */
    async download(formData: Omit<IDownloadFileData, 'user_id'>) {
      const user_id = userStore.user_id
      if (!user_id) return
      try {
        const res = await downloadFile({ ...formData, user_id })
        const { code, msg, data } = res.data
        if (code === 200) {
          return data
        } else {
          throw new Error(msg)
        }
      } catch (e) {
        throw new Error((e as Error).message)
      }
    }
  }
})

export default useFileStore

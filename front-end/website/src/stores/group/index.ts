import { defineStore } from 'pinia'
import type { IGroupStoreState } from './type'
import {
  createGroup,
  getGroupList,
  type ICreateGroupData,
  type IGetGroupListData
} from '@/views/group/api'
import {
  addProjectMember,
  createProject,
  deleteProjectMember,
  getProjectList,
  getProjectMembers,
  updateProjectStatus,
  type IAddProjectMemberData,
  type ICreateProjectData,
  type IDeleteProjectMemberData,
  type IGetProjectListData,
  type IGetProjectMembersData,
  type IUpdateProjectStatusData,
  type IUpdateProjectData,
  updateProject
} from '@/views/workplace/project/api'
import {
  updateGroupInfo,
  type IUpdateGroupInfo,
  type IGetGroupMembersData,
  getGroupMembers,
  type IDeleteGroupMemberData,
  deleteGroupMember,
  type IAddGroupMemberData,
  addGroupMember,
  type IUpdateGroupStatusData,
  updateGroupStatus
} from '@/views/group/api/setting'
import useUserStore from '../user'

const useGroupStore = defineStore('group', {
  state: (): IGroupStoreState => ({
    groups: [],
    projects: []
  }),
  getters: {
    /**
     * 获取指定 ID 的组织信息
     * @returns
     */
    getGroupById() {
      return (id: number) => {
        return this.groups.find((g) => g.group_id === id)
      }
    },
    /**
     * 获取指定 ID 的项目信息
     */
    getProjectById(state) {
      return (id: number) => {
        return state.projects.find((p) => p.project_id === id)
      }
    }
  },
  actions: {
    /**
     * 创建组织
     * @param {ICreateGroupData} formData
     * @returns
     */
    async create(formData: ICreateGroupData) {
      try {
        const res = await createGroup(formData)
        const { code, msg } = res.data
        if (code === 200) {
          return true
        } else {
          throw new Error(msg)
        }
      } catch (e) {
        throw new Error((e as Error).message)
      }
    },
    /**
     * 请求获取组织列表
     */
    async getGroups(formData: Partial<IGetGroupListData> = {}) {
      this.$reset()
      try {
        const res = await getGroupList(formData)
        const { code, data, msg } = res.data
        if (code === 200) {
          this.groups = data.joined_group
          return data.joined_group
        } else {
          throw new Error(msg)
        }
      } catch (e) {
        throw new Error((e as Error).message)
      }
    },
    /**
     * 请求获取项目列表
     */
    async getProjects(formData: IGetProjectListData) {
      try {
        const res = await getProjectList(formData)
        const { code, data, msg } = res.data
        if (code === 200) {
          this.projects = data
        } else {
          throw new Error(msg)
        }
      } catch (e) {
        throw new Error((e as Error).message)
      }
    },
    /**
     * 更新组织信息
     * @param formData 表单信息
     * @returns
     */
    async updateGroupData(formData: Partial<IUpdateGroupInfo>) {
      const userStore = useUserStore()
      const user_id = userStore.user_id
      if (!user_id) return
      const { icon, name, id } = formData
      if (!id) return
      // 待传入数据
      const data: IUpdateGroupInfo = { id, creator_id: user_id }
      // 默认需要携带 name
      data.name = name
      // 验证表单是否发生改变，减少数据传入
      if (icon !== this.getGroupById(id)?.icon && icon !== '') data.icon = icon
      // 提交请求
      try {
        const res = await updateGroupInfo(data)
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
     * 获取组织成员列表
     */
    async getMembers(formData: IGetGroupMembersData) {
      try {
        const res = await getGroupMembers(formData)
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
     * 添加组织成员
     * @param formData
     * @returns
     */
    async addMember(formData: Omit<IAddGroupMemberData, 'creator_id'>) {
      // 获取当前登录的用户 id
      const userStore = useUserStore()
      const creator_id = userStore.user_id
      if (!creator_id) return
      const data: IAddGroupMemberData = {
        creator_id,
        ...formData
      }
      // 提交表单
      try {
        const res = await addGroupMember(data)
        return res.data
      } catch (e) {
        throw new Error((e as Error).message)
      }
    },
    /**
     * 删除成员
     * @param formData 表单信息
     * @returns
     */
    async deleteMember(formData: Omit<IDeleteGroupMemberData, 'creator_id'>) {
      // 获取当前登录的用户 id
      const userStore = useUserStore()
      const creator_id = userStore.user_id
      if (!creator_id) return
      const data: IDeleteGroupMemberData = {
        creator_id,
        ...formData
      }
      // 提交请求
      try {
        const res = await deleteGroupMember(data)
        return res.data
      } catch (e) {
        throw new Error((e as Error).message)
      }
    },
    /**
     * 创建项目
     * @param formData 表单信息
     * @returns
     */
    async createProject(formData: Omit<ICreateProjectData, 'creator_id'>) {
      // 获取当前登录的用户 id
      const userStore = useUserStore()
      const creator_id = userStore.user_id
      if (!creator_id) return
      const data: ICreateProjectData = {
        creator_id,
        ...formData
      }
      // 提交请求
      try {
        const res = await createProject(data)
        return res.data
      } catch (e) {
        throw new Error((e as Error).message)
      }
    },
    /**
     * 获取项目成员列表
     */
    async getProjectMembers(formData: IGetProjectMembersData) {
      try {
        const res = await getProjectMembers(formData)
        const { code, data, msg } = res.data
        if (code === 200) {
          return data.project_list
        } else {
          throw new Error(msg)
        }
      } catch (e) {
        throw new Error((e as Error).message)
      }
    },
    /**
     * 删除项目成员
     * @param formData 表单信息
     * @returns
     */
    async deleteProjectMember(formData: Omit<IDeleteProjectMemberData, 'creator_id'>) {
      // 获取当前登录的用户 id
      const userStore = useUserStore()
      const creator_id = userStore.user_id
      if (!creator_id) return
      const data: IDeleteProjectMemberData = {
        creator_id,
        ...formData
      }
      // 提交请求
      try {
        const res = await deleteProjectMember(data)
        return res.data
      } catch (e) {
        throw new Error((e as Error).message)
      }
    },
    /**
     * 添加项目成员
     * @param formData
     * @returns
     */
    async addProjectMember(formData: Omit<IAddProjectMemberData, 'creator_id'>) {
      // 获取当前登录的用户 id
      const userStore = useUserStore()
      const creator_id = userStore.user_id
      if (!creator_id) return
      const data: IAddProjectMemberData = {
        creator_id,
        ...formData
      }
      // 提交表单
      try {
        const res = await addProjectMember(data)
        return res.data
      } catch (e) {
        throw new Error((e as Error).message)
      }
    },
    /**
     * 开启或关闭项目
     * @param formData
     * @returns
     */
    async dismissProject(formData: Omit<IUpdateProjectStatusData, 'creator_id'>) {
      // 获取当前登录的用户 id
      const userStore = useUserStore()
      const creator_id = userStore.user_id
      if (!creator_id) return
      const data: IUpdateProjectStatusData = {
        creator_id,
        ...formData
      }
      // 提交表单
      try {
        const res = await updateProjectStatus(data)
        return res.data
      } catch (e) {
        throw new Error((e as Error).message)
      }
    },
    /**
     * 开启或关闭组织
     * @param formData
     * @returns
     */
    async dismissGroup(formData: Omit<IUpdateGroupStatusData, 'creator_id'>) {
      // 获取当前登录的用户 id
      const userStore = useUserStore()
      const creator_id = userStore.user_id
      if (!creator_id) return
      const data: IUpdateGroupStatusData = {
        creator_id,
        ...formData
      }
      // 提交表单
      try {
        const res = await updateGroupStatus(data)
        return res.data
      } catch (e) {
        throw new Error((e as Error).message)
      }
    },
    /**
     * 更新项目信息
     * @param formData
     * @returns
     */
    async updateProjectData(formData: Omit<IUpdateProjectData, 'creator_id'>) {
      const userStore = useUserStore()
      const user_id = userStore.user_id
      if (!user_id) return
      const { group_id, project_id, icon, name } = formData
      if (!group_id || !project_id) return
      // 待传入数据
      const data: IUpdateProjectData = { group_id, project_id, creator_id: user_id }
      // 默认需要携带 name
      data.name = name
      // 验证表单是否发生改变，减少数据传入
      if (icon !== this.getProjectById(project_id)?.project_info.icon && icon !== '')
        data.icon = icon
      if (name !== this.getProjectById(project_id)?.project_info.name && name !== '')
        data.name = name
      // 提交请求
      try {
        const res = await updateProject(data)
        const { code, data: ret, msg } = res.data
        if (code === 200) {
          return ret
        } else {
          throw new Error(msg)
        }
      } catch (e) {
        throw new Error((e as Error).message)
      }
    }
  }
})

export default useGroupStore

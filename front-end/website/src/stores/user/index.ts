import { clearToken, setToken } from '@/utils/token'
import { login as userLogin, logout as userLogout, type ILoginData } from '@/views/login/api/login'
import { defineStore } from 'pinia'
import useTheme, { ETheme } from '@/hooks/theme'
import type { IUserState } from './type'
import { register as userRegister, type IRegisterData } from '@/views/register/api'
import { Message } from '@arco-design/web-vue'
import { getProfile, updateProfile, type IUpdateProfileData } from '@/views/profile/api'

const useUserStore = defineStore('user', {
  state: (): Partial<IUserState> => ({
    user_id: undefined,
    email: undefined,
    nickname: undefined,
    avatar: undefined,
    bio: undefined,
    setting: {
      theme: undefined,
      lang: undefined,
      side: undefined,
      frame: undefined
    }
  }),
  actions: {
    /**
     * 用户登录
     * @param {ILoginData} formData
     */
    async login(formData: ILoginData) {
      // 重置本 store 中所有状态
      this.$reset()
      try {
        const res = await userLogin(formData)
        setToken(res.data.data.token)
        this.setUserState({
          email: formData.username,
          ...res.data.data
        })
      } catch (e) {
        clearToken()
        throw e
      }
    },
    /**
     * 设置 UserStore 状态
     * @param {Partial<IUserState>} data
     */
    setUserState(data: Partial<IUserState>) {
      const { email, nickname, avatar, setting, user_id, bio } = data
      if (user_id !== undefined) this.user_id = user_id
      if (email !== undefined) this.email = email
      if (nickname !== undefined) this.nickname = nickname
      if (avatar !== undefined) this.avatar = avatar
      if (bio !== undefined) this.bio = bio
      if (setting !== undefined) {
        const { currentTheme } = useTheme()
        const { theme } = setting
        this.setting = setting
        if (theme) {
          currentTheme.value = theme as ETheme
        }
        // 更改语言需要在 setup 里调用
      }
    },
    /**
     * 用户注册
     * @param formData
     */
    async register(formData: IRegisterData) {
      // 重置本 store 中所有状态
      this.$reset()
      try {
        const res = await userRegister(formData)
        Message.success(res.data.data)
        return res.data.data
      } catch (e) {
        throw new Error((e as Error).message)
      }
    },
    /**
     * 退出登录
     */
    async logout() {
      await userLogout()
      clearToken()
      this.$reset()
    },
    /**
     * 获取用户信息
     * @returns 用户信息
     */
    async getUserData() {
      if (!this.user_id) return
      try {
        const res = await getProfile({ user_id: this.user_id })
        const { code, data, msg } = res.data
        if (code === 200) {
          const { nickname, avatar, bio, setting } = data
          this.setUserState({ nickname, avatar, bio, setting })
          return data
        } else {
          throw new Error(msg)
        }
      } catch (e) {
        throw new Error((e as Error).message)
      }
    },
    /**
     * 更新用户信息
     * @param formData 表单信息
     * @returns
     */
    async updateUserData(formData: Partial<IUpdateProfileData>) {
      if (!this.user_id) return
      const { bio, avatar, nickname, lang, theme, frame, side } = formData
      // 待传入数据
      const data: IUpdateProfileData = { user_id: this.user_id }
      // 验证表单是否发生改变，减少数据传入
      if (bio && bio !== this.bio && bio !== '') data.bio = bio
      if (avatar && avatar !== this.avatar && avatar !== '') data.avatar = avatar
      if (nickname && nickname !== this.nickname && nickname !== '') data.nickname = nickname
      if (lang && lang !== '') data.lang = lang
      if (theme && theme !== '') data.theme = theme
      if (frame?.toString() && frame?.toString() != String(this.setting?.frame)) data.frame = frame
      if (side && side !== this.setting?.side && side !== '') data.side = side
      // 只有一个 user_id 就不请求
      if (Object.keys(data).length === 1) return
      // 提交请求
      try {
        const res = await updateProfile(data)
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
  },
  persist: true
})

export default useUserStore

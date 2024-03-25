import localeHome from '@/views/home/locale/zh-CN'
import localeLogin from '@/views/login/locale/zh-CN'
import localeRegister from '@/views/register/locale/zh-CN'
import localeGroup from '@/views/group/locale/zh-CN'
import localeUpload from '@/components/upload/locale/zh-CN'
import localeWorkplace from '@/views/workplace/locale/zh-CN'
import localeProfile from '@/views/profile/locale/zh-CN'

export default {
  'navbar.action.locale': '切换中文',

  'coresite.footer.text': '© 2024 心界团队 版权所有',

  'settings.language': '切换语言',
  'settings.theme': '切换主题',
  'settings.goback': '返回首页',
  'settings.user.changeGroup': '切换组织',
  'settings.user.profile': '个人信息',
  'settings.user.logout': '退出登录',
  'settings.group.modal.title': '组织设置',

  'settings.otherSetting.title': '更多设置',

  ...localeHome,
  ...localeLogin,
  ...localeRegister,
  ...localeGroup,
  ...localeUpload,
  ...localeWorkplace,
  ...localeProfile
}

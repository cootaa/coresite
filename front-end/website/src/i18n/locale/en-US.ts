import localeHome from '@/views/home/locale/en-US'
import localeLogin from '@/views/login/locale/en-US'
import localeRegister from '@/views/register/locale/en-US'
import localeGroup from '@/views/group/locale/en-US'
import localeUpload from '@/components/upload/locale/en-US'
import localeWorkplace from '@/views/workplace/locale/en-US'
import localeProfile from '@/views/profile/locale/en-US'

export default {
  'navbar.action.locale': 'Switch to English',

  'coresite.footer.text': 'Copyright © 2024 CORESITE Team. All rights reserved.',

  'settings.language': 'Switch Language',
  'settings.theme': 'Switch Theme',
  'settings.goback': 'Back to Home',
  'settings.user.changeGroup': 'Switch Group',
  'settings.user.profile': 'Your Profile',
  'settings.user.logout': 'Sign out',
  'settings.group.modal.title': 'Group Setting',

  'settings.otherSetting.title': 'Setting',

  ...localeHome,
  ...localeLogin,
  ...localeRegister,
  ...localeGroup,
  ...localeUpload,
  ...localeWorkplace,
  ...localeProfile
}

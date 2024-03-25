import type { Router } from 'vue-router'

import { isLogin } from '@/utils/token'
import useUserStore from '@/stores/user'
import { unloginRouteName } from '..'

/**
 * 建立用户路由守卫
 * 防止未登录查看工作台
 * @param router
 */
export default function setupUserGuard(router: Router) {
  router.beforeEach(async (to, from, next) => {
    const userStore = useUserStore()
    // 判断是否存在 Token 跟 用户 id
    if (isLogin() && userStore.user_id) {
      next()
    } else {
      // 判断是否前往不需要登录的页面
      if (unloginRouteName.includes(to.name as string)) {
        next()
      } else {
        next({ name: 'login' })
      }
    }
  })
}

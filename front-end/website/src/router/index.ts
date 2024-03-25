import { createRouter, createWebHistory } from 'vue-router'
import createRouterGuard from './guard'

// 免登录路由名称
export const unloginRouteName = ['home', 'user', 'login', 'register']

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'home',
      component: () => import('../views/home/index.vue')
    },
    {
      path: '/user',
      name: 'user',
      component: () => import('../layout/Classical.vue'),
      props: {
        noBorder: true,
        noUser: true,
        center: true
      },
      children: [
        {
          path: 'login',
          name: 'login',
          component: () => import('../views/login/index.vue')
        },
        {
          path: 'register',
          name: 'register',
          component: () => import('../views/register/index.vue')
        }
      ]
    },
    {
      path: '/group',
      name: 'group',
      component: () => import('../layout/Classical.vue'),
      props: {
        noBorder: true,
        noBack: true
      },
      children: [
        {
          path: 'list',
          name: 'groupList',
          component: () => import('../views/group/list/index.vue')
        }
      ]
    },
    {
      path: '/workplace/:group_id',
      name: 'workplace',
      component: () => import('../views/workplace/index.vue'),
      children: [
        {
          path: 'project/:project_id',
          name: 'project',
          component: () => import('../views/workplace/project/index.vue')
        }
      ]
    }
  ]
})

createRouterGuard(router)

export default router

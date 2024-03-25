import type { Router } from 'vue-router'
import setupUserGuard from './user'
import setupWorkplaceGuard from './workplace'

export default function createRouterGuard(router: Router) {
  setupUserGuard(router)
  setupWorkplaceGuard(router)
}

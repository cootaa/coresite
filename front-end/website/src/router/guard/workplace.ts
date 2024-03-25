import type { Router } from 'vue-router'

import useChatStore from '@/stores/chat'

import { Message } from '@arco-design/web-vue'

/**
 * 建立工作台路由守卫
 * 用于 WebSocket ...
 * @param router
 */
export default function setupWorkplaceGuard(router: Router) {
  router.beforeEach(async (to, from, next) => {
    const { socket, connectSocket, disconnectSocket, joinRoom, leaveAllRooms } = useChatStore()
    if (to.name === 'workplace' || to.name === 'project') {
      // 建立 Socket 链接
      if (!socket.id) {
        connectSocket().catch((err) => {
          Message.error(err.message)
        })
      }
      // 加入房间
      if (to.name === 'project') {
        // 进入新房间
        const room_id = Number(to.params.project_id)
        if (room_id) joinRoom(room_id)
      }
    } else {
      // 非项目页 && 组织页断开连接
      if (socket.id) {
        disconnectSocket()
      }
      // 离开已经加入的房间
      leaveAllRooms()
    }
    next()
  })
}

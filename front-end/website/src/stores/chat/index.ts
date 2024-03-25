import {
  getChatList as getChatListAPI,
  sendMessage,
  type IGetChatListData
} from '@/views/workplace/project/api/chat'
import { defineStore } from 'pinia'
import useUserStore from '../user'

import { io } from 'socket.io-client'
import type { Socket } from 'socket.io-client'

import moment from 'moment'

import { ref } from 'vue'
import { WSUrl } from '@/config/common'

import mitt, { type Emitter } from 'mitt'

const emitter: Emitter<IMessageEvents> = mitt<IMessageEvents>()

const useChatStore = defineStore('chat', () => {
  const userStore = useUserStore()
  // Socket
  const socket = ref<any>({})
  // Socket ID
  const userSocketId = ref<string>()
  // 已加入的房间
  const rooms = ref<number[]>([])
  // 获取到新消息的房间 ID
  const msgRoomId = ref<number[]>([])

  /**
   * 收到新消息的回调
   * @param msg
   * @param room_id
   */
  const handleReceiveMessage = (msg: string, room_id: number) => {
    // 触发 receive 事件
    emitter.emit('receive', {
      msg,
      room_id
    })
    if (msgRoomId.value.includes(room_id)) return
    msgRoomId.value.push(room_id)
  }

  return {
    socket,
    msgRoomId,
    userSocketId,
    /**
     * 获取聊天数据
     * @param formData
     * @returns
     */
    async getChatList(formData: Omit<IGetChatListData, 'user_id'>) {
      const user_id = userStore.user_id
      if (!user_id) return
      try {
        const res = await getChatListAPI({ ...formData, user_id })
        const { code, data, msg } = res.data
        if (code === 200) {
          return data
        } else {
          throw new Error(msg)
        }
      } catch (e) {
        throw e as Error
      }
    },
    /**
     * 链接 Socket
     */
    connectSocket() {
      return new Promise((resolve, rejects) => {
        // 链接 Socket
        const ws = io(WSUrl, {
          withCredentials: true
        })
        ws.on('connect', () => {
          userSocketId.value = socket.value.id as string
          resolve(socket.value)
          if (!socket.value.id) rejects(new Error('connect fail'))
        })
        socket.value = ws
        // 监听消息
        ws.on('chat', handleReceiveMessage)
      })
    },
    /**
     * 断开 Socket
     */
    disconnectSocket() {
      ;(socket.value as Socket).disconnect()
    },
    /**
     * 是否加入过某房间
     * @param room_id
     */
    isJoinedRoom(room_id: number) {
      return rooms.value.includes(room_id)
    },
    /**
     * 加入房间
     * @param room_id 房间 ID / 项目 ID
     */
    joinRoom(room_id: number) {
      const user_id = userStore.user_id
      if (!user_id) return
      if (this.isJoinedRoom(room_id)) return
      ;(socket.value as Socket).emit('joinRoom', { room_id, user_id })
      rooms.value.push(room_id)
    },
    /**
     * 离开房间
     */
    leaveRoom(room_id: number) {
      const user_id = userStore.user_id
      if (!user_id) return
      if (!this.isJoinedRoom(room_id)) return
      ;(socket.value as Socket).emit('joinRoom', { user_id, room_id })
      rooms.value = rooms.value.filter((item) => item !== room_id)
    },
    /**
     * 离开所有房间
     */
    leaveAllRooms() {
      rooms.value.forEach(this.leaveRoom)
    },
    /**
     * 发送消息
     * @param room_id 房间 ID
     * @param message 发送的内容
     * @returns
     */
    async sendMessage(room_id: number, message: string) {
      const { user_id, nickname, avatar } = userStore
      if (!user_id || !nickname || !avatar) return
      if (!this.isJoinedRoom(room_id)) return
      try {
        const create_time = moment(new Date().getTime()).format('YYYY-MM-DD HH:mm:ss')
        const data: IMessageItem = {
          message,
          room_id,
          user_id,
          user: {
            avatar,
            nickname
          },
          create_time
        }
        // 对 Socket 发送
        ;(socket.value as Socket).emit('msg', {
          room_id,
          msg: JSON.stringify(data)
        })
        // 对存储发送
        await sendMessage({
          message,
          user_id,
          project_id: room_id
        })
        return data
      } catch (e) {
        throw e as Error
      }
    },
    /**
     * 从新消息提醒中移除
     * @param id 房间 ID
     */
    removeMsgAlert(id: number) {
      msgRoomId.value = msgRoomId.value.filter((item) => Number(item) !== id)
    }
  }
})

export default useChatStore

export { emitter }

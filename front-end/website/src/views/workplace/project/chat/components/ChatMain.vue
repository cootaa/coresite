<template>
  <a-split
    class="coresite-chat-main"
    direction="vertical"
    :style="{ height: '100%' }"
    v-model:size="size"
    min="0.3"
    max="0.9"
  >
    <template #first>
      <ChatList :messages="chatList" @reach-bottom="getChatList" />
    </template>
    <template #second>
      <ChatArea @on-handle-resize="handleResize" @on-handle-push-message="handlePushMessage" />
    </template>
  </a-split>
</template>

<script lang="ts" setup>
import { computed, onMounted, ref } from 'vue'

import { useRoute } from 'vue-router'

import useChatStore, { emitter } from '@/stores/chat'

import ChatList from './ChatList.vue'
import ChatArea from './ChatArea.vue'
import type { IGetChatListResponse } from '../../api/chat'

const route = useRoute()
const chatStore = useChatStore()

// 项目 ID
const projectId = computed(() => Number(route.params.project_id))
// 分割大小
const size = ref(0.9)
// 变更窗口大小
const handleResize = (s: number) => {
  size.value = s
}
// 每次获取聊天数量
const chatSize = ref(10)
// 当前页数
const currentPage = ref(0)
// 是否有更多聊天消息
const hasMore = ref(true)
// 聊天数据
const chatList = ref<IMessageItem[]>([])

onMounted(() => {
  handleReceiveMessage()
})

/**
 * 初始化接收消息
 */
const handleReceiveMessage = () => {
  emitter.on('receive', ({ msg, room_id }) => {
    if (Number(room_id) === projectId.value) {
      handlePushMessage(JSON.parse(msg))
    }
  })
}

/**
 * 插入消息
 * @param message 消息
 * @param pos 位置 0 为前面，1 为后面
 */
const handlePushMessage = (message: IMessageItem, pos: 0 | 1 = 0) => {
  if (pos === 0) {
    chatList.value.unshift(message)
  } else {
    chatList.value.push(message)
  }
}

/**
 * 获取消息列表
 */
const getChatList = () => {
  // 验证表单
  if (!projectId.value) return
  if (!hasMore.value) return
  // 提交表单
  currentPage.value += 1
  chatStore
    .getChatList({
      page: currentPage.value,
      size: chatSize.value,
      project_id: projectId.value
    })
    .then((res) => {
      const list = (res as IGetChatListResponse).chat_list
      list.forEach((item) => handlePushMessage(item, 1))
      if (res?.size && res?.size < chatSize.value) {
        hasMore.value = false
      }
    })
}
</script>

<style lang="less">
.coresite-chat-main {
  .arco-split-pane {
    position: relative;
  }
}
</style>

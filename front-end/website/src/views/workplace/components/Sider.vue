<template>
  <!-- Sidebar -->
  <a-layout-sider
    class="coresite-project-list"
    :width="180"
    v-model:collapsed="sideCollapsed"
    collapsible
    @collapse="handleCollapse"
  >
    <a-menu v-model:selected-keys="selectedKeys" :default-selected-keys="['home']">
      <!-- Discuss -->
      <a-menu-item key="home" @click="handleGotoDiscuss">
        <template #icon>
          <icon-home size="large" />
        </template>
        {{ $t('workplace.sider.discuss') }}
      </a-menu-item>
      <!-- Project List -->
      <a-menu-item
        v-for="item in projects"
        :key="`${item.project_id}`"
        @click="() => handleClick(item.project_id)"
      >
        <template #icon>
          <div class="msg-alert" v-if="hasNewMessage(item.project_id)"></div>
          <component :is="'icon-' + (item.project_info.icon ?? 'qq-zone')" size="large" />
        </template>
        {{ item.project_info.name }}
      </a-menu-item>
      <!-- New Project -->
      <a-menu-item key="create" @click="handleCreateProject">
        <template #icon>
          <icon-plus size="large" />
        </template>
        {{ $t('workplace.sider.new') }}
      </a-menu-item>
      <a-divider v-if="closedProjects.length" />
      <!-- Close Project -->
      <a-menu-item
        class="closed-project"
        v-for="item in closedProjects"
        :key="`${item.project_id}`"
        @click="() => handleClick(item.project_id)"
      >
        <template #icon>
          <div class="msg-alert" v-if="hasNewMessage(item.project_id)"></div>
          <component :is="'icon-' + (item.project_info.icon ?? 'qq-zone')" size="large" />
        </template>
        {{ item.project_info.name }}
      </a-menu-item>
    </a-menu>
  </a-layout-sider>
  <!-- Create Project Modal -->
  <Modal
    modal-class="create-modal"
    v-model:visible="createProjectVisible"
    :title="$t('workplace.sider.new')"
    @before-open="handleCreateProjectBeforeOpen"
  >
    <NewProject :key="renderKey" />
  </Modal>
</template>

<script lang="ts" setup>
import { computed, ref, watch, watchEffect } from 'vue'
import { storeToRefs } from 'pinia'

import { useRouter, useRoute } from 'vue-router'

import useGroupStore from '@/stores/group'
import useChatStore from '@/stores/chat'
import useUserStore from '@/stores/user'

import Modal from '@/layout/Modal.vue'
import NewProject from '@/views/workplace/project/new/Index.vue'

const router = useRouter()
const route = useRoute()

const groupStore = useGroupStore()
const chatStore = useChatStore()
const userStore = useUserStore()

// 当前打开的项目 ID
const projectId = computed(() => Number(route.params.project_id))
// 项目列表
const projects = computed(() => groupStore.projects.filter((item) => item.status === 1))
// 已关闭项目
const closedProjects = computed(() => groupStore.projects.filter((item) => item.status !== 1))
// 新建项目是否可见
const createProjectVisible = ref(false)
// 用于重新渲染 NewProject 组件
const renderKey = ref(new Date().getTime())
// 用于重新渲染 NewProject 组件
const handleCreateProjectBeforeOpen = () => {
  renderKey.value = new Date().getTime()
}
// 获取到新消息的房间 ID
const { msgRoomId } = storeToRefs(chatStore)
// 移除消息提示
const { removeMsgAlert } = chatStore
// 选中的导航 ID
const selectedKeys = ref<string[]>()
// 导航折叠
const sideCollapsed = ref(false)

// 监听导航设置
watch(
  () => userStore.setting,
  (setting) => {
    if (setting) {
      const collapsed = setting.side === 'Indent'
      sideCollapsed.value = collapsed
    }
  }
)

// 监听路由变化设置选中导航
watchEffect(() => {
  if (projectId.value) {
    selectedKeys.value = [projectId.value.toString()]
  }
})
// 监听选中导航
watch(selectedKeys, (newVal, oldVal) => {
  // 如果是新建项目，则不设置导航选中
  if (newVal?.includes('create')) {
    selectedKeys.value = oldVal
  }
})

/**
 * 新建项目
 */
const handleCreateProject = () => {
  createProjectVisible.value = true
}

/**
 * 单击导航跳转
 */
const handleClick = (project_id: number) => {
  // 移除消息提示
  removeMsgAlert(project_id)
  // 路由跳转
  router.push({ name: 'project', params: { project_id } })
}
/**
 * 跳转到讨论
 */
const handleGotoDiscuss = () => {
  const group_id = route.params.group_id
  router.push({ name: 'workplace', params: { group_id } })
}

/**
 * 判断是否有新消息
 * @param id 项目 ID
 */
const hasNewMessage = computed(() => (id: number) => {
  // 如果是当前打开的项目则不显示小红点
  if (id === projectId.value) return false
  return msgRoomId.value.findIndex((item) => Number(item) === id) !== -1
})

/**
 * 折叠状态改变时触发
 * @param collapsed 折叠状态
 * @param type 触发类型
 */
const handleCollapse = (collapsed: boolean, type: string) => {
  if (type === 'clickTrigger') {
    const collapse = collapsed ? 'Indent' : 'Outdent'
    userStore.updateUserData({
      side: collapse
    })
  }
}
</script>

<style lang="less">
.coresite-project-list {
  .msg-alert {
    position: absolute;
    top: 8px;
    right: 5px;
    width: 5px;
    height: 5px;
    border-radius: 50%;
    background-color: red;
    box-sizing: content-box;
  }

  .closed-project {
    opacity: 0.5;
  }
}
</style>

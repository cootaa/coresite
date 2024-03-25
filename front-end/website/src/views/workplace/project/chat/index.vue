<template>
  <div class="coresite-chat">
    <a-card
      :id="settingArea"
      class="coresite-chat-container"
      :title="props.project?.project_info.name"
    >
      <!-- Project Setting -->
      <template #extra>
        <a-space align="center" fill>
          <!-- Project Members -->
          <a-avatar-group class="z-index-0" :size="24" :max-count="5">
            <a-avatar v-for="item in memberList" :key="item.user.nickname">
              <img :src="item.user.avatar" />
            </a-avatar>
          </a-avatar-group>
          <!-- Project Setting Button -->
          <a-tooltip :content="$t('workplace.header.setting')">
            <a-button @click="handleShowSetting" type="text">
              <template #icon>
                <icon-more-vertical />
              </template>
            </a-button>
          </a-tooltip>
        </a-space>
      </template>
      <!-- Project Setting Drawer -->
      <Setting
        :group-id="props.groupId"
        :project-id="props.projectId"
        :project="props.project"
        :popup-container="'#' + settingArea"
        :pop-container="'#' + settingArea"
        :visible="settingVisible"
        @cancel="handleHideSetting"
        @onHandleUpdateMemberList="handleUpdateMemberList"
      />
      <!-- Project Chat -->
      <ChatMain />
    </a-card>
  </div>
</template>

<script lang="ts" setup>
import { ref } from 'vue'

import type { IProjectItem } from '../api'

import Setting from '../setting/index.vue'
import ChatMain from './components/ChatMain.vue'
import type { IMemberType } from '@/views/group/api/setting'

const props = defineProps<{
  groupId: number
  projectId: number
  project?: IProjectItem
}>()

// 设置是否可见
const settingVisible = ref(false)
// 设置弹窗的挂载点
const settingArea = 'coresite-chat-area'
// 成员列表
const memberList = ref<IMemberType[]>([])

// 更新成员列表
const handleUpdateMemberList = (members: IMemberType[]) => {
  memberList.value = members
}

// 设置点击反馈
const handleShowSetting = () => {
  settingVisible.value = true
}
const handleHideSetting = () => {
  settingVisible.value = false
}
</script>

<style lang="less">
.coresite-chat {
  width: 100%;
  height: 100%;
  padding: 12px;

  &-container {
    position: relative;
    width: 100%;
    height: 100%;
    overflow: hidden;

    .arco-card-body {
      position: absolute;
      padding: 0;
      width: 100%;
      height: calc(100% - 46px);
    }
  }
}
</style>

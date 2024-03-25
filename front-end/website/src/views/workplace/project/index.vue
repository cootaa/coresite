<template>
  <a-split
    :style="{
      height: '100%',
      width: '100%'
    }"
    v-model:size="size"
    min="0.3"
    max="0.7"
  >
    <template #first>
      <Chat :group-id="group_id" :project-id="project_id" :project="currentProject" />
    </template>
    <template #second>
      <File :group-id="group_id" :project-id="project_id" :project="currentProject" />
    </template>
  </a-split>
</template>

<script lang="ts" setup>
import { computed, ref, watch } from 'vue'
import { useRoute } from 'vue-router'
import { watchThrottled } from '@vueuse/core'

import useGroupStore from '@/stores/group'
import useUserStore from '@/stores/user'

import Chat from './chat/index.vue'
import File from './file/index.vue'

const route = useRoute()

const userStore = useUserStore()
const groupStore = useGroupStore()

// 当前分割大小
const size = ref<number>(0.35)
// 当前组织 ID
const group_id = computed(() => Number(route.params.group_id))
// 当前项目 ID
const project_id = computed(() => Number(route.params.project_id))
// 当前项目信息
const currentProject = computed(() => groupStore.getProjectById(project_id.value))

// 监听设置变化
watch(
  () => userStore.setting,
  (setting) => {
    if (setting) {
      size.value = Number(setting.frame)
    }
  }
)
// 监听分割大小变化
watchThrottled(
  size,
  (newSize) => {
    if (newSize) {
      userStore.updateUserData({
        frame: Number(newSize)
      })
    }
  },
  {
    throttle: 1000
  }
)
</script>

<script lang="ts">
export default {
  name: 'Project'
}
</script>

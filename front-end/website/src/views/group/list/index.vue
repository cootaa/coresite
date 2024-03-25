<template>
  <a-spin :loading="loading">
    <a-grid :row-gap="32" :col-gap="32" :cols="{ xs: 1, sm: 1, md: 3, lg: 24 }" class="group-list">
      <!-- Group List -->
      <a-grid-item :span="{ xs: 1, sm: 1, md: 2, lg: 16 }">
        <GroupList :list="groups" />
      </a-grid-item>
      <!-- Create Group or Join -->
      <a-grid-item :span="{ xs: 1, sm: 1, md: 1, lg: 8 }">
        <a-space direction="vertical" fill>
          <NoGroup />
          <CreateGroup />
        </a-space>
      </a-grid-item>
    </a-grid>
  </a-spin>
</template>

<script lang="ts" setup>
import { onMounted, computed } from 'vue'
import { Message } from '@arco-design/web-vue'

import useLoading from '@/hooks/loading'

import useGroupStore from '@/stores/group'
import useUserStore from '@/stores/user'

import NoGroup from '../components/NoGroup.vue'
import CreateGroup from '../components/CreateGroup.vue'
import GroupList from '../components/GroupList.vue'

const groupStore = useGroupStore()
const userStore = useUserStore()

const { loading, setLoading } = useLoading()

// 组织列表
const groups = computed(() => groupStore.groups)
// 有无组织
const nogroup = computed(() => groups.value.length === 0)

onMounted(() => {
  getGroups()
})

/**
 * 获取组织
 */
const getGroups = () => {
  const { user_id } = userStore
  // 判断是否登录
  if (!user_id) return
  // 提交表单
  setLoading(true)
  groupStore
    .getGroups({ user_id })
    .catch((err) => {
      Message.error(err.message)
    })
    .finally(() => {
      setLoading(false)
    })
}
</script>

<script lang="ts">
export default {
  name: 'ChooseGroup'
}
</script>

<style lang="less" setup>
.group-list {
  margin: 0 !important;
  padding: 24px;
}
</style>

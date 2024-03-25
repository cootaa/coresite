<template>
  <a-space class="other-setting" direction="vertical" :size="12" fill>
    <a-space class="other-setting-item" direction="vertical" fill>
      <span class="other-setting-item-title">
        {{ $t('group.setting.group.status') }}
        {{ groupStatus }}
      </span>
      <a-popconfirm :content="groupStatusPS" @ok="handleGroupCloseOrOpen" position="bottom">
        <a-button status="danger">{{ groupStatusButton }}</a-button>
      </a-popconfirm>
    </a-space>
  </a-space>
</template>

<script lang="ts" setup>
import { computed } from 'vue'

import { useRoute } from 'vue-router'
import { useI18n } from 'vue-i18n'

import useLoading from '@/hooks/loading'
import useGroupStore from '@/stores/group'
import { Message } from '@arco-design/web-vue'

const route = useRoute()
const groupStore = useGroupStore()

const { t } = useI18n()
const { loading, setLoading } = useLoading()

// 当前组织 ID
const currentGroupId = computed(() => Number(route.params.group_id))
// 当前组织信息
const currentGroup = computed(() => groupStore.getGroupById(currentGroupId.value))
// 当前组织是否开启
const isGroupOpen = computed(() => currentGroup.value?.status === 1)
// 组织关闭开启文案
const groupStatus = computed(() => {
  return isGroupOpen.value ? t('group.setting.close.title') : t('group.setting.open.title')
})
const groupStatusButton = computed(() => {
  return isGroupOpen.value ? t('group.setting.close.button') : t('group.setting.open.button')
})
const groupStatusPS = computed(() => {
  return isGroupOpen.value ? t('group.setting.close.ps') : t('group.setting.open.ps')
})

/**
 * 关闭或开启组织
 */
const handleGroupCloseOrOpen = () => {
  // 验证表单
  if (!currentGroupId.value) return
  // 提交表单
  setLoading(true)
  groupStore
    .dismissGroup({
      group_id: currentGroupId.value
    })
    .then((res) => {
      Message.success(res?.data as string)
    })
    .finally(() => {
      setTimeout(() => {
        window.location.reload()
      }, 1000)
    })
}
</script>

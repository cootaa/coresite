<template>
  <a-spin :loading="loading">
    <a-space direction="vertical" :size="12" fill>
      <!-- Upload Icon -->
      <a-row align="center" :gutter="12">
        <a-col flex="none">
          <UploadImage :defaultUrl="icon" @update:image-url="changeImageUrl" />
        </a-col>
        <a-col flex="auto">
          {{ $t('group.create.upload.ps') }}
        </a-col>
      </a-row>
      <!-- Group Name -->
      <a-input :placeholder="$t('group.create.input.name')" v-model="name"></a-input>
      <!-- Submit -->
      <a-space align="end" fill>
        <a-button type="primary" @click="handleSubmit">{{ $t('group.setting.submit') }}</a-button>
      </a-space>
    </a-space>
  </a-spin>
</template>

<script lang="ts" setup>
import { computed, onMounted, ref } from 'vue'

import { useRoute } from 'vue-router'
import { useI18n } from 'vue-i18n'

import useGroupStore from '@/stores/group'
import useLoading from '@/hooks/loading'

import UploadImage from '@/components/upload/Image.vue'
import { Message } from '@arco-design/web-vue'
import type { IGroupItem } from '../api'

const route = useRoute()
const groupStore = useGroupStore()

const { t } = useI18n()
const { loading, setLoading } = useLoading(true)

// 当前组织 ID
const group_id = computed(() => Number(route.params.group_id))
// 组织标识
const icon = ref()
// 组织名称
const name = ref()

/**
 * 修改组织标识
 */
const changeImageUrl = (url: string) => {
  icon.value = url
}

onMounted(() => {
  getGroupInfo()
})

/**
 * 获取组织信息
 */
const getGroupInfo = () => {
  setLoading(true)
  new Promise((resolve, reject) => {
    if (!groupStore.groups.length) resolve({})
    // 获取当前组织 ID
    if (!group_id.value) {
      reject(t('group.setting.getinfo.error'))
    }
    // 获取当前组织信息
    const groupInfo = groupStore.getGroupById(group_id.value)
    if (!groupInfo) {
      reject(t('group.setting.getinfo.error'))
    }
    resolve(groupInfo)
  })
    .then((res) => {
      const data = res as IGroupItem
      icon.value = data.icon
      name.value = data.name
    })
    .catch((err) => {
      Message.error(err)
    })
    .finally(() => {
      setLoading(false)
    })
}

/**
 * 保存组织信息
 */
const handleSubmit = () => {
  // 验证表单
  if (icon.value === '' || icon.value === null) {
    return Message.error(t('group.setting.save.icon.empty'))
  }
  if (name.value === '' || name.value === null) {
    return Message.error(t('group.setting.save.name.empty'))
  }
  // 更新组织信息
  setLoading(true)
  groupStore
    .updateGroupData({
      id: group_id.value,
      name: name.value,
      icon: icon.value
    })
    .then(() => {
      Message.success(t('group.setting.save.success'))
    })
    .finally(() => {
      setTimeout(() => {
        window.location.reload()
      }, 1000)
    })
}
</script>

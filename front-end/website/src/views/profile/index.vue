<template>
  <a-tabs position="left">
    <a-tab-pane key="1" :title="$t('profile.tabs.setting')">
      <a-spin :loading="loading">
        <Base :profile="profile" @on-before-submit="onBeforeSubmit" @on-finally="onFinally" />
      </a-spin>
    </a-tab-pane>
    <a-tab-pane key="2" :title="$t('settings.otherSetting.title')">
      <a-spin :loading="loading">
        <Other :profile="profile" @on-before-submit="onBeforeSubmit" @on-finally="onFinally" />
      </a-spin>
    </a-tab-pane>
  </a-tabs>
</template>

<script lang="ts" setup>
import { onMounted, ref } from 'vue'

import useLoading from '@/hooks/loading'
import useUserStore from '@/stores/user'

import type { IGetProfileResponse } from './api'

import Base from './components/Base.vue'
import Other from './components/Other.vue'

const userStore = useUserStore()

const { loading, setLoading } = useLoading(true)

// 用户信息
const profile = ref<IGetProfileResponse>()

onMounted(async () => {
  getUserProfile()
})

/**
 * 信息提交前 emit
 */
const onBeforeSubmit = () => {
  setLoading(true)
}
/**
 * 信息提交完成 emit
 */
const onFinally = () => {
  setLoading(false)
  // 重新获取用户信息
  getUserProfile()
}

/**
 * 获取用户信息
 */
const getUserProfile = () => {
  setLoading(true)
  userStore
    .getUserData()
    .then((res) => {
      // 设置用户信息
      profile.value = res as IGetProfileResponse
    })
    .finally(() => {
      setLoading(false)
    })
}
</script>

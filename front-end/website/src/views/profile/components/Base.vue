<template>
  <a-space direction="vertical" :size="12" fill>
    <!-- Upload Avatar -->
    <a-row align="center" :gutter="12">
      <a-col flex="none">
        <UploadImage :defaultUrl="avatar" @update:image-url="changeImageUrl" />
      </a-col>
      <a-col flex="auto">
        {{ $t('profile.setting.avatar.upload.ps') }}
      </a-col>
    </a-row>
    <!-- Show Email -->
    <a-input
      :placeholder="$t('profile.setting.email.ps')"
      :default-value="email"
      readonly
    ></a-input>
    <!-- Nickname -->
    <a-input :placeholder="$t('profile.setting.nickname.ps')" v-model="nickname"></a-input>
    <!-- Bio -->
    <a-textarea
      :placeholder="$t('profile.setting.bio.ps')"
      :auto-size="{ minRows: 2 }"
      v-model="bio"
    />
    <!-- Submit -->
    <a-space align="end" fill>
      <a-button type="primary" @click="handleSubmit">{{ $t('profile.setting.submit') }}</a-button>
    </a-space>
  </a-space>
</template>

<script lang="ts" setup>
import { computed, ref, watch } from 'vue'

import type { IGetProfileResponse } from '../api'

import { useI18n } from 'vue-i18n'
import useUserStore from '@/stores/user'

import UploadImage from '@/components/upload/Image.vue'
import { Message } from '@arco-design/web-vue'

const props = defineProps<{
  profile?: IGetProfileResponse
}>()
const emit = defineEmits(['onFinally', 'onBeforeSubmit'])

const userStore = useUserStore()

const { t } = useI18n()

// 用户头像
const avatar = ref()
// 用户昵称
const nickname = ref()
// 用户简介
const bio = ref()
// 用户邮箱，仅展示
const email = computed(() => userStore.email)
// 监听用户数据是否改变
watch(
  () => props.profile,
  (val) => {
    avatar.value = val?.avatar
    nickname.value = val?.nickname
    bio.value = val?.bio
  }
)

/**
 * 修改用户头像
 */
const changeImageUrl = (url: string) => {
  avatar.value = url
}

/**
 * 提交修改
 */
const handleSubmit = () => {
  // 昵称不能为空
  if (nickname.value === '' || nickname.value === null)
    return Message.error(t('profile.setting.nickname.empty'))
  emit('onBeforeSubmit')
  userStore
    .updateUserData({
      avatar: avatar.value,
      bio: bio.value,
      nickname: nickname.value
    })
    .then(() => {
      // 修改成功返回提示
      Message.success(t('profile.setting.success'))
    })
    .finally(() => {
      emit('onFinally')
    })
}
</script>

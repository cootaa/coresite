<template>
  <a-space class="group-nogroup-content" direction="vertical" size="large" fill>
    <!-- No Group Title -->
    <span class="group-title">{{ $t('group.nogroup.title') }}</span>
    <!-- Copy Email -->
    <a-row :gutter="12">
      <a-col :span="16">
        <a-input size="large" :default-value="email" readonly />
      </a-col>
      <a-col :span="8">
        <a-button type="primary" size="large" @click="handleCopy">{{
          $t('group.nogroup.copy')
        }}</a-button>
      </a-col>
    </a-row>
    <!-- PS -->
    <span class="group-ps">{{ $t('group.nogroup.ps') }}</span>
    <!-- OR -->
    <span class="group-title group-or">{{ $t('group.nogroup.or') }}</span>
  </a-space>
</template>

<script lang="ts" setup>
import { computed } from 'vue'
import { copyTextToClipboard } from '@/utils/common'
import { Message } from '@arco-design/web-vue'

import { useI18n } from 'vue-i18n'
import useUserStore from '@/stores/user'

const userStore = useUserStore()
const { t } = useI18n()

const email = computed(() => userStore.email)

/**
 * 复制文本
 */
const handleCopy = () => {
  copyTextToClipboard(email.value as string)
    .then(() => {
      Message.success(t('group.nogroup.copy.success'))
    })
    .catch(() => {
      Message.success(t('group.nogroup.copy.error'))
    })
}
</script>

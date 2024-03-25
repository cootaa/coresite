<template>
  <!-- Button -->
  <a-tooltip :content="$t('workplace.header.setting')">
    <a-button @click="handleClick" type="text">
      <template #icon>
        <icon-more-vertical />
      </template>
    </a-button>
  </a-tooltip>
  <!-- Setting -->
  <Modal
    v-model:visible="settingVisible"
    :title="$t('settings.group.modal.title')"
    @before-open="handleBeforeOpen"
  >
    <Setting :key="renderKey" />
  </Modal>
</template>

<script lang="ts" setup>
import { ref } from 'vue'
import Modal from '@/layout/Modal.vue'
import Setting from '@/views/group/setting/index.vue'

// 设置可见
const settingVisible = ref()
// 用于重新渲染 Setting 组件
const renderKey = ref(new Date().getTime())

const handleBeforeOpen = () => {
  renderKey.value = new Date().getTime()
}

const handleClick = () => {
  settingVisible.value = true
}
</script>

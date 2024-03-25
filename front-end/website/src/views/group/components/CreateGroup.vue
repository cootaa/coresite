<template>
  <a-space
    class="group-button justify-content-center center-min-width"
    align="center"
    fill
    @click="handleClick"
  >
    <icon-plus class="group-button-icon" />
    <span class="group-button-title">{{ $t('group.create') }}</span>
  </a-space>
  <Modal
    modal-class="create-modal"
    v-model:visible="createVisible"
    :title="$t('settings.group.modal.title')"
  >
    <Create :key="new Date().getTime()" />
  </Modal>
</template>

<script lang="ts" setup>
import { ref } from 'vue'

import Modal from '@/layout/Modal.vue'
import Create from '@/views/group/create/index.vue'

const createVisible = ref(false)

const handleClick = () => {
  createVisible.value = true
}
</script>

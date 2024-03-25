<template>
  <a-dropdown position="br">
    <a-button type="outline" size="small">
      <template #icon>
        <icon-plus />
      </template>
    </a-button>
    <template #content>
      <a-doption @click="handleUpdate">{{ $t('workplace.project.file.option.upload') }}</a-doption>
      <a-doption @click="handleCreateFolder">{{
        $t('workplace.project.file.option.create')
      }}</a-doption>
    </template>
  </a-dropdown>
  <!-- Upload Files -->
  <Modal
    v-model:visible="uploadVisible"
    :title="$t('workplace.project.file.option.upload')"
    @before-close="handleAfterUpdate"
  >
    <UploadFiles
      :visible="uploadVisible"
      :folder-id="props.folderInfo?.id"
      :key="new Date().getTime()"
    />
  </Modal>
</template>

<script lang="ts" setup>
import type { IFolderItem } from '../../api/file'

import Modal from '@/layout/Modal.vue'
import UploadFiles from '@/components/upload/Files.vue'
import { ref } from 'vue'

const props = defineProps<{
  folderInfo?: IFolderItem
}>()

const emit = defineEmits(['onHandleCreateFolder', 'onBeforeUpdate', 'onAfterUpdate'])

const uploadVisible = ref(false)

const handleUpdate = () => {
  uploadVisible.value = true
}
const handleCreateFolder = () => {
  emit('onHandleCreateFolder')
}

const handleAfterUpdate = () => {
  emit('onAfterUpdate')
}
</script>

<template>
  <a-dropdown position="br">
    <a-button type="text" size="small">
      <template #icon>
        <icon-more />
      </template>
    </a-button>
    <template #content>
      <a-doption
        v-for="item in options"
        :key="item.value"
        :value="item.value"
        :disabled="item.disabled"
        @click="item.action"
        >{{ $t(item.label) }}</a-doption
      >
    </template>
  </a-dropdown>
  <Modal
    v-model:visible="removeVisible"
    :title="$t('workplace.project.remove.title')"
    @before-close="handleUpdate"
  >
    <Remove
      :type="props.type"
      :project-id="props.projectId"
      :info="props.info"
      :key="new Date().getTime()"
      @on-handle-update="handleUpdate"
    />
  </Modal>
</template>

<script lang="ts">
export enum EListFileType {
  Folder,
  File
}
export enum EListFileOpenType {
  Rename,
  Download,
  Remove,
  Delete
}
</script>

<script lang="ts" setup>
import { reactive, ref } from 'vue'

import type { IFileItem, IFolderItem } from '../../api/file'

import { useI18n } from 'vue-i18n'
import useFileStore from '@/stores/file'

import Modal from '@/layout/Modal.vue'
import Remove from './Remove.vue'

import { Message, Modal as AModal } from '@arco-design/web-vue'

const props = defineProps<{
  projectId?: number
  type: EListFileType
  info: IFolderItem | IFileItem
}>()

const emit = defineEmits(['onHandleUpdated', 'onHandleRename'])

const { t } = useI18n()

const fileStore = useFileStore()

// 移动文件是否可见
const removeVisible = ref(false)

/**
 * 文件重命名
 */
const handleRename = () => {
  emit('onHandleRename', props.info.id)
}
/**
 * 文件下载
 */
const handleDownload = () => {
  if (props.type === EListFileType.File) {
    const item = props.info as IFileItem
    fileStore
      .download({
        file_id: item.id
      })
      .then((res) => {
        if (res) {
          window.open(res.download_url)
        }
      })
  }
}
/**
 * 文件移动
 */
const handleRemove = () => {
  removeVisible.value = true
}
/**
 * 文件删除
 */
const handleDelete = () => {
  // 验证表单信息
  if (!props.projectId) return
  if (!props.info) return
  // 弹窗提示
  AModal.error({
    title: t('workplace.project.file.delete.title'),
    content: t('workplace.project.file.delete.content'),
    okText: t('workplace.project.file.delete.submit'),
    cancelText: t('workplace.project.file.delete.quit'),
    hideCancel: false,
    modalStyle: {
      textAlign: 'center'
    },
    // 确认删除
    onOk: () => {
      let promise: Promise<string | undefined>
      if (props.type === EListFileType.File) {
        // 文件类型
        promise = fileStore.deleteFile({
          folder_id: (props.info as IFileItem).folder_id,
          file_id: (props.info as IFileItem).id
        })
      } else {
        // 文件夹类型
        promise = fileStore.deleteFolder({
          project_id: props.projectId as number,
          folder_id: (props.info as IFolderItem).id
        })
      }
      promise
        .then((res) => {
          Message.success(res as string)
        })
        .finally(() => {
          emit('onHandleUpdated')
        })
    }
  })
}

const options = reactive([
  {
    value: EListFileOpenType.Rename,
    label: 'workplace.project.file.option.rename',
    action: handleRename
  },
  {
    value: EListFileOpenType.Download,
    label: 'workplace.project.file.option.download',
    disabled: props.type === EListFileType.Folder,
    action: handleDownload
  },
  {
    value: EListFileOpenType.Remove,
    label: 'workplace.project.file.option.remove',
    action: handleRemove
  },
  {
    value: EListFileOpenType.Delete,
    label: 'workplace.project.file.option.delete',
    action: handleDelete
  }
])

const handleUpdate = () => {
  removeVisible.value = false
  emit('onHandleUpdated')
}
</script>

<template>
  <a-space class="upload-files" direction="vertical" fill>
    <!-- File List -->
    <a-list class="upload-files-list" :bordered="false" size="small" max-height="60vh">
      <a-list-item v-for="item in files" :key="item.uid">
        <a-row align="center" justify="space-between" :gutter="12">
          <!-- File Name -->
          <a-col flex="auto">{{ item.name }}</a-col>
          <!-- File Size -->
          <a-col flex="none">{{ filesize(item.file?.size as number) }}</a-col>
          <!-- Upload Status -->
          <a-col flex="none">
            <!-- Uploading -->
            <a-button type="text" size="small" v-if="item.status === 'uploading'">
              <template #icon>
                <icon-loading />
              </template>
            </a-button>
            <!-- Done -->
            <a-button status="success" size="small" v-else-if="item.status === 'done'">
              <template #icon>
                <icon-check />
              </template>
            </a-button>
            <!-- Cover -->
            <a-button
              status="warning"
              size="small"
              v-else-if="item.status === 'error' && item.response === 0"
              @click="handleRetry(item, 1)"
            >
              <template #icon>
                <icon-exclamation type="warning" />
              </template>
            </a-button>
            <!-- Retry -->
            <a-button status="error" size="small" v-else @click="handleRetry(item, 2)">
              <template #icon>
                <icon-stop />
              </template>
            </a-button>
          </a-col>
        </a-row>
      </a-list-item>
    </a-list>
    <!-- Actions -->
    <a-row justify="end">
      <a-col flex="none">
        <a-upload
          v-model:fileList="files"
          :show-file-list="false"
          :custom-request="customRequest"
          ref="uploadRef"
          multiple
        ></a-upload>
      </a-col>
    </a-row>
  </a-space>
</template>

<script lang="ts" setup>
import {
  Message,
  type FileItem,
  type RequestOption,
  type UploadInstance,
  Modal
} from '@arco-design/web-vue'
import { computed, ref } from 'vue'
import { useRoute } from 'vue-router'
import { filesize } from 'filesize'

import { useI18n } from 'vue-i18n'
import useFileStore from '@/stores/file'

const props = defineProps<{
  folderId?: number
  visible: boolean
}>()

const route = useRoute()
const fileStore = useFileStore()

const { t } = useI18n()

// 当前组织 ID
const group_id = computed(() => Number(route.params.group_id))
// 当前项目 ID
const project_id = computed(() => Number(route.params.project_id))
// 上传文件列表
const files = ref<FileItem[]>([])
// 上传按钮 ref
const uploadRef = ref<UploadInstance>()

/**
 * 文件重新上传
 * @param item 文件项
 * @param type 提示类型：1-覆盖 2-重试
 */
const handleRetry = (item: FileItem, type: 1 | 2) => {
  const title = type === 1 ? t('upload.cover.title') : t('upload.retry.title')
  const content = type === 1 ? t('upload.cover.content') : t('upload.retry.content')
  Modal.warning({
    title,
    content,
    hideCancel: false,
    modalClass: 'text-center',
    onOk: () => {
      if (uploadRef.value) {
        uploadRef.value.submit(item)
      }
    }
  })
}
/**
 * 文件上传
 */
const customRequest = (option: RequestOption) => {
  const { fileItem, onSuccess, onError } = option
  const confirm = fileItem.response === 0 ? 1 : 0
  // 验证表单
  if (!props.folderId) return
  if (!group_id.value) return
  if (!project_id.value) return
  if (fileItem.file) {
    fileStore
      .uploadFile({
        confirm,
        group_id: group_id.value,
        project_id: project_id.value,
        folder_id: props.folderId,
        file: fileItem.file
      })
      .then((res) => {
        if (res?.data === 1) {
          Message.success(res?.msg as string)
          onSuccess()
        } else {
          Message.warning(res?.msg as string)
          onError(0)
        }
      })
      .catch((err) => {
        Message.error((err as Error).message)
        onError()
      })
  }
}
</script>

<style lang="less" scoped>
.upload-files {
  &-list {
    min-height: 50vh;
  }
}
</style>

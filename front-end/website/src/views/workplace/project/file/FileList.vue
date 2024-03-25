<template>
  <a-list class="file-list" :bordered="false" :split="false" size="small" :hoverable="true">
    <template #empty></template>
    <a-list-item v-for="item in props.fileList" :key="item.id">
      <template #meta>
        <a-row align="center" justify="space-between">
          <a-col flex="none">
            <!-- File Name -->
            <a-link
              class="text-only1-line"
              @click="() => handleOpenFile(item)"
              v-if="renameFileId !== item.id"
              >{{ item.name + item.format }}</a-link
            >
            <!-- File Rename -->
            <Rename
              v-else
              :default-value="item.name"
              :id="item.id"
              :folder-id="item.folder_id"
              :placeholder="$t('workplace.project.file.create.ps')"
              @on-handle-submit="handleFileRename"
              @on-handle-close="handleFileRenameClose"
            />
          </a-col>
          <!-- File Info -->
          <a-col class="file-meta" flex="none">
            <a-row align="center" :gutter="12">
              <!-- File Size -->
              <a-col :span="9">
                <div class="file-size text-right text-only1-line">{{ filesize(item.size) }}</div>
              </a-col>
              <!-- Update Time -->
              <a-col :span="9">
                <div class="file-update-time text-center text-only1-line">
                  {{ getFormatTime(item.update_time) }}
                </div>
              </a-col>
              <!-- File Uploader -->
              <a-col :span="6">
                <div class="file-uploader text-only1-line">{{ item.creator.nickname }}</div>
              </a-col>
            </a-row>
          </a-col>
        </a-row>
      </template>
      <template #actions>
        <FileActions
          :project-id="props.projectId"
          :type="EListFileType.File"
          :info="item"
          @on-handle-updated="handleUpdated"
          @on-handle-rename="handleRename"
        />
      </template>
    </a-list-item>
  </a-list>
  <!-- Image Preview -->
  <a-image-preview-group
    v-model:visible="previewVisible"
    v-model:current="previewCurrent"
    infinite
    :srcList="srcList"
  />
</template>

<script lang="ts" setup>
import { ref, watch } from 'vue'
import { filesize } from 'filesize'
import type { IFileItem } from '../api/file'

import moment from 'moment'

import useFileStore from '@/stores/file'
import { useI18n } from 'vue-i18n'

import FileActions, { EListFileType } from './components/FileActions.vue'
import Rename from './components/Rename.vue'
import { Message } from '@arco-design/web-vue'

const props = defineProps<{
  projectId?: number
  fileList?: IFileItem[]
}>()

const emit = defineEmits(['onHandleBeforeUpdate', 'onHandleUpdated'])

const fileStore = useFileStore()
const { t, locale } = useI18n()

// 图片预览
const srcList = ref<string[]>([])
const previewVisible = ref(false)
const previewCurrent = ref<number>()
// 文件更名
const renameFileId = ref<number>()

// 监听文件列表设置图片预览链接数组
watch(
  () => props.fileList,
  (list) => {
    const urls = list?.filter((item) => item.type.includes('image')).map((item) => item.url)
    if (urls) {
      srcList.value = urls
    }
  }
)

const getFormatTime = (time: string) => {
  const a = moment(time).locale(locale.value.toLocaleLowerCase())
  return a.fromNow()
}

/**
 * 打开文件
 */
const handleOpenFile = (item: IFileItem) => {
  // 目标为图片类型打开图片预览
  if (item.type.includes('image')) {
    const index = srcList.value.findIndex((url) => url === item.url)
    previewCurrent.value = index
    previewVisible.value = true
  }
}

/**
 * 文件夹更名
 */
const handleRename = (id: number) => {
  renameFileId.value = id
}
/**
 * 文件夹确认更名
 */
const handleFileRename = (id: number, name: string, folder_id: number) => {
  // 验证表单
  if (!id || !name) return
  if (!folder_id) return
  // 提交表单
  emit('onHandleBeforeUpdate')
  fileStore
    .fileRename({
      name,
      folder_id,
      file_id: id
    })
    .then(() => {
      Message.success(t('workplace.project.rename.success'))
    })
    .finally(() => {
      handleFileRenameClose()
      emit('onHandleUpdated')
    })
}
/**
 * 取消更名
 */
const handleFileRenameClose = () => {
  renameFileId.value = undefined
}
/**
 * 数据更新
 */
const handleUpdated = () => {
  emit('onHandleUpdated')
}
</script>

<style lang="less" scoped>
.file-list {
  .file-meta {
    min-width: 25rem;
    color: var(--color-neutral-8);
  }
}
</style>

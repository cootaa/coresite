<template>
  <a-list class="folder-list" :bordered="false" size="small" :split="false" :hoverable="true">
    <!-- No Folder -->
    <template #empty></template>
    <!-- Create Folder -->
    <a-list-item v-if="createVisible">
      <a-list-item-meta>
        <template #title>
          <a-space class="folder-list-create" align="center">
            <!-- New Folder Name -->
            <a-input
              v-model="newFolderName"
              :placeholder="$t('workplace.project.folder.create.ps')"
            ></a-input>
            <!-- New Folder Save -->
            <a-button type="primary" @click="handleCreateFolder">
              <template #icon>
                <icon-check />
              </template>
            </a-button>
            <!-- New Folder Exit -->
            <a-button @click="handleHiddenCreateFolder">
              <template #icon>
                <icon-close />
              </template>
            </a-button>
          </a-space>
        </template>
      </a-list-item-meta>
    </a-list-item>
    <!-- Folder List -->
    <a-list-item v-for="item in props.folderList" :key="item.id">
      <a-list-item-meta>
        <!-- Folder Name -->
        <template #title>
          <a-link @click="() => handleOpenFolder(item.id)" v-if="renameFolderId !== item.id">
            <a-space>
              <icon-folder />
              <span>
                {{ item.name }}
              </span>
            </a-space>
          </a-link>
          <!-- Folder Rename -->
          <Rename
            v-else
            :default-value="item.name"
            :id="item.id"
            :placeholder="$t('workplace.project.folder.create.ps')"
            @on-handle-submit="handleFolderRename"
            @on-handle-close="handleFolderRenameClose"
          />
        </template>
      </a-list-item-meta>
      <template #actions>
        <!-- Folder Actions -->
        <FileActions
          :project-id="props.projectId"
          :type="EListFileType.Folder"
          :info="item"
          @on-handle-updated="handleUpdated"
          @on-handle-rename="handleRename"
        />
      </template>
    </a-list-item>
  </a-list>
</template>

<script lang="ts" setup>
import { ref } from 'vue'
import type { IFolderItem } from '../api/file'

import { useI18n } from 'vue-i18n'

import useFileStore from '@/stores/file'

import FileActions, { EListFileType } from './components/FileActions.vue'
import Rename from './components/Rename.vue'
import { Message } from '@arco-design/web-vue'

const props = defineProps<{
  projectId: number
  folderInfo?: IFolderItem
  folderList?: IFolderItem[]
  createVisible: boolean
}>()

const emit = defineEmits([
  'onHandleOpenFolder',
  'onHandleBeforeUpdate',
  'onHandleUpdated',
  'onHandleHiddenCreateFolder'
])

const { t } = useI18n()

const fileStore = useFileStore()

// 新建文件夹名称
const newFolderName = ref('')

// 重命名文件夹 id
const renameFolderId = ref<number>()

/**
 * 打开目录
 */
const handleOpenFolder = (id: number) => {
  emit('onHandleOpenFolder', id)
}

/**
 * 提交新建文件夹
 */
const handleCreateFolder = () => {
  const folderName = newFolderName.value
  const parentFolderId = props.folderInfo?.id
  // 验证表单
  if (!folderName || !parentFolderId || !props.projectId) return
  // 提交表单
  emit('onHandleBeforeUpdate')
  fileStore
    .createFolder({
      project_id: props.projectId,
      parent_id: parentFolderId,
      name: folderName
    })
    .finally(() => {
      emit('onHandleUpdated')
      handleHiddenCreateFolder()
    })
}
/**
 * 隐藏新建文件夹
 */
const handleHiddenCreateFolder = () => {
  newFolderName.value = ''
  emit('onHandleHiddenCreateFolder')
}
/**
 * 文件夹更名
 */
const handleRename = (id: number) => {
  renameFolderId.value = id
}
/**
 * 文件夹确认更名
 */
const handleFolderRename = (id: number, name: string) => {
  // 验证表单
  if (!id || !name) return
  if (!props.projectId) return
  // 提交表单
  emit('onHandleBeforeUpdate')
  fileStore
    .folderRename({
      name,
      folder_id: id,
      project_id: props.projectId
    })
    .then(() => {
      Message.success(t('workplace.project.rename.success'))
    })
    .finally(() => {
      handleFolderRenameClose()
      emit('onHandleUpdated')
    })
}
/**
 * 取消更名
 */
const handleFolderRenameClose = () => {
  renameFolderId.value = undefined
}
/**
 * 数据更新
 */
const handleUpdated = () => {
  emit('onHandleUpdated')
}
</script>

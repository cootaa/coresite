<template>
  <a-spin class="coresite-file" :loading="loading">
    <a-card class="coresite-file-container">
      <!-- Path -->
      <template #title>
        <a-breadcrumb>
          <template #separator>
            <icon-right />
          </template>
          <a-breadcrumb-item v-for="item in path" :key="item.folder_id">
            <a-link @click="() => handleGoFolder(item)">{{ item.name }}</a-link>
          </a-breadcrumb-item>
        </a-breadcrumb>
      </template>
      <!-- File Actions -->
      <template #extra>
        <FolderActions
          :folder-info="currentFolderInfo"
          @on-handle-create-folder="handleCreateFolder"
          @on-before-update="beforeUpdate"
          @on-after-update="afterUpdate"
        />
      </template>
      <!-- Folder List -->
      <FolderList
        :project-id="projectId"
        :folder-info="currentFolderInfo"
        :folder-list="folderList"
        :create-visible="createFolderVisible"
        @on-handle-open-folder="handleOpenFolder"
        @on-handle-hidden-create-folder="handleHiddenCreateFolder"
        @on-handle-before-update="beforeUpdate"
        @on-handle-updated="afterUpdate"
      >
      </FolderList>
      <!-- File List -->
      <FileList
        :project-id="projectId"
        :file-list="fileList"
        @on-handle-before-update="beforeUpdate"
        @on-handle-updated="afterUpdate"
      ></FileList>
    </a-card>
  </a-spin>
</template>

<script lang="ts" setup>
import { onMounted, ref } from 'vue'

import type { IProjectItem } from '../api'

import useLoading from '@/hooks/loading'
import useFileStore from '@/stores/file'

import usePath, { type IPathItem } from '@/hooks/path'
import type { IFileItem, IFolderItem } from '../api/file'

import FolderActions from './components/FolderActions.vue'
import FolderList from './FolderList.vue'
import FileList from './FileList.vue'

const props = defineProps<{
  groupId: number
  projectId: number
  project?: IProjectItem
}>()

const fileStore = useFileStore()

const { loading, setLoading } = useLoading(true)

const { path, currentPath, addPath, goPath } = usePath()

// 当前文件夹信息
const currentFolderInfo = ref<IFolderItem>()
// 文件夹列表
const folderList = ref<IFolderItem[]>()
// 文件列表
const fileList = ref<IFileItem[]>()
// 新建文件夹
const createFolderVisible = ref(false)

onMounted(() => {
  getFileList()
})

/**
 * 获取文件列表
 */
const getFileList = (folder_id?: number) => {
  // 无项目 ID
  if (!props.projectId) return
  // 请求数据
  setLoading(true)
  fileStore
    .getFileFolders({ project_id: props.projectId, folder_id })
    .then((res) => {
      const folderInfo = res.folder_info
      const subFolders = folderInfo.sub_folders
      const subFiles = folderInfo.files
      // 判断当前路径是否需要添加路径
      if (!currentPath.value || folderInfo.id !== currentPath.value.folder_id) {
        addPath({
          folder_id: folderInfo.id,
          name: folderInfo.name
        })
      }
      currentFolderInfo.value = folderInfo
      folderList.value = subFolders
      fileList.value = subFiles
    })
    .finally(() => {
      setLoading(false)
    })
}
// 打开文件
const handleOpenFolder = (folderId: number) => {
  getFileList(folderId)
}
// 去到某个目录
const handleGoFolder = (pathItem: IPathItem) => {
  goPath(pathItem)
  getFileList(pathItem.folder_id)
}
// 新建目录
const handleCreateFolder = () => {
  createFolderVisible.value = true
}
// 隐藏新建目录
const handleHiddenCreateFolder = () => {
  createFolderVisible.value = false
}
// 文件上传前执行
const beforeUpdate = () => {
  setLoading(true)
}
// 文件上传后执行
const afterUpdate = () => {
  setLoading(false)
  getFileList(currentFolderInfo.value?.id)
}
</script>

<style lang="less">
.coresite-file {
  width: 100%;
  height: 100%;
  padding: 12px;

  &-container {
    position: relative;
    height: 100%;

    .arco-card-body {
      position: absolute;
      width: 100%;
      height: calc(100% - 20px - 1.9rem);
      overflow: auto;
    }
  }

  // &-list {
  //   height: 100%;
  //   overflow: hidden;
  // }

  .arco-list-item {
    padding: 5px 0 !important;
  }
}
</style>

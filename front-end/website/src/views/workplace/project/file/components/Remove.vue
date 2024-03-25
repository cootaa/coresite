<template>
  <a-space class="coresite-remove" direction="vertical" :size="12" fill>
    <a-scrollbar type="track" style="height: 350px; overflow: auto">
      <a-tree v-model:selected-keys="selectedKeys" blockNode :data="folderTree" />
    </a-scrollbar>
    <div class="text-right">
      <a-button type="primary" @click="handleSubmit">{{
        $t('workplace.project.remove.button')
      }}</a-button>
    </div>
  </a-space>
</template>

<script lang="ts" setup>
import { computed, onMounted, ref } from 'vue'

import type { IFileItem, IFolderItem, IGetFolderTreeInfo } from '../../api/file'
import type { HttpResponse } from '@/utils/interceptor'

import useFileStore from '@/stores/file'
import { useI18n } from 'vue-i18n'

import { EListFileType } from './FileActions.vue'
import { Message, Modal, type TreeNodeData } from '@arco-design/web-vue'

const props = defineProps<{
  projectId?: number
  type: EListFileType
  info: IFolderItem | IFileItem
}>()

const emit = defineEmits(['onHandleUpdate'])

const fileStore = useFileStore()

const { t } = useI18n()

// 文件树
const folderTree = computed(() => {
  if (!fileStore.tree.folder) return []
  const data: TreeNodeData[] = [getTreeNode(fileStore.tree.folder)]
  return data
})
// 选中位置
const selectedKeys = ref<number[]>([])

/**
 * 获取目录树
 * @param node 目录
 */
const getTreeNode = (node: IGetFolderTreeInfo, disabled = false): TreeNodeData => {
  let children: TreeNodeData[] = []
  // 判断移动的目标文件夹是否为当前文件夹
  if (props.type === EListFileType.Folder) {
    if (node.id === props.info.id) {
      disabled = true
    }
  }
  // 判断是否有子文件夹
  if (node.sub_folders) {
    children = node.sub_folders.map((item) => getTreeNode(item, disabled))
  }
  return {
    children,
    disabled,
    title: node.name,
    key: node.id
  }
}

onMounted(() => {
  getFolderTree()
})

/**
 * 从服务器获取文件树
 */
const getFolderTree = () => {
  // 减少重复查询
  if (fileStore.tree.status === 'loading') return
  // 验证表单
  if (!props.projectId) return
  // 提交表单
  fileStore.getFolderTree({
    project_id: props.projectId
  })
}

const handleSubmit = () => {
  // 验证表单
  if (!props.info) return
  if (!selectedKeys.value.length) return Message.error(t('workplace.project.remove.select.none'))
  // 判断移动类型
  let promise: Promise<HttpResponse<unknown> | undefined>
  if (props.type === EListFileType.Folder) {
    promise = fileStore.removeFolder({
      folder_id: props.info.id,
      parent_id: selectedKeys.value[0]
    })
  } else {
    promise = fileStore.removeFile({
      file_id: props.info.id,
      folder_id: selectedKeys.value[0]
    })
  }
  promise
    .then((res) => {
      // 文件与目标文件夹文件重名
      if (typeof res?.data === 'number') {
        if (res.data === 2) {
          return 0
        }
      }
      Message.success(res?.msg as string)
    })
    .then((res) => {
      // 判断文件是否已经存在
      if (res === 0) {
        onFileExist(props.info.id, selectedKeys.value[0])
      }
    })
    .finally(() => {
      emit('onHandleUpdate')
    })
}

/**
 * 文件已存在弹窗
 */
const onFileExist = (file_id: number, folder_id: number) => {
  Modal.warning({
    title: t('upload.cover.title'),
    content: t('upload.cover.content'),
    hideCancel: false,
    modalClass: 'text-center',
    onOk: () => {
      fileStore
        .removeFile({
          file_id,
          folder_id,
          confirm: 1 // 文件覆盖
        })
        .then((res) => {
          if (typeof res?.data === 'number' && res.data === 1) {
            Message.success(res?.msg as string)
          }
        })
        .finally(() => {
          emit('onHandleUpdate')
        })
    }
  })
}
</script>

<style lang="less">
.coresite-remove {
  .arco-tree-node-selected .arco-tree-node-title {
    color: var(--color-warning-light-4);
  }
}
</style>

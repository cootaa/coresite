<template>
  <Modal :title="$t('workplace.discuss.discussion.new')">
    <a-space
      class="create-discussion"
      direction="vertical"
      :size="16"
      fill
      :key="new Date().getTime()"
    >
      <!-- Title -->
      <a-input
        v-model="title"
        :placeholder="$t('workplace.discuss.discussion.create.title')"
        :error="!title"
      />
      <!-- Editor -->
      <Editor
        class="create-discussion-editor"
        :api-key="apiKey"
        v-model="content"
        :init="editorInit"
      />
      <!-- Button -->
      <a-row align="center" justify="end">
        <a-button type="primary" @click="handleCreateDiscussion">{{
          $t('workplace.discuss.discussion.create.button')
        }}</a-button>
      </a-row>
    </a-space>
  </Modal>
</template>

<script lang="ts" setup>
import { reactive, ref } from 'vue'
import { EditorApiKey } from '@/config/common'

import { useI18n } from 'vue-i18n'

import useWorkspaceStore from '@/stores/workspace'

import { Message } from '@arco-design/web-vue'

import Modal from '@/layout/Modal.vue'
import Editor from '@tinymce/tinymce-vue'

const props = defineProps<{
  groupId: number
}>()

const emits = defineEmits(['onHandleUpdate'])

const { t } = useI18n()

const { createDiscussion } = useWorkspaceStore()

// 讨论标题
const title = ref('')
// 讨论内容
const content = ref('')
// 编辑器 API Key
const apiKey = EditorApiKey

// 编辑器配置
const editorInit = reactive({
  menubar: false,
  toolbar_mode: 'floating'
})

/**
 * 创建讨论
 */
const handleCreateDiscussion = () => {
  // 验证表单
  if (!props.groupId) return
  if (!title.value) return Message.error(t('workplace.discuss.discussion.create.title.ps'))
  if (!content.value) return Message.error(t('workplace.discuss.discussion.create.content.ps'))
  // 提交表单
  createDiscussion({
    group_id: props.groupId,
    title: title.value,
    content: content.value
  }).then((res) => {
    Message.success(res?.msg as string)
    title.value = ''
    content.value = ''
    emits('onHandleUpdate')
  })
}
</script>

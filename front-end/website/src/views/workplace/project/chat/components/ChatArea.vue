<template>
  <a-space class="coresite-chat-area" direction="vertical" :size="0" fill>
    <!-- Editor -->
    <template v-if="visible">
      <Editor
        class="create-discussion-editor"
        :api-key="apiKey"
        v-model="content"
        :init="editorInit"
        @blur="handleBlur"
      />
    </template>
    <a-row
      :justify="visible ? 'end' : 'space-between'"
      align="center"
      style="padding: 12px; margin: 0"
      :gutter="12"
    >
      <a-col v-if="!visible" flex="auto">
        <a-input @click="handleShowEditor" placeholder="Type your message" />
      </a-col>
      <a-col flex="none">
        <a-button type="primary" size="small" @click="handleSendMessage" :loading="loading"
          >Send</a-button
        >
      </a-col>
    </a-row>
  </a-space>
</template>

<script lang="ts" setup>
import { computed, reactive, ref } from 'vue'
import { EditorApiKey as apiKey } from '@/config/common'

import { useRoute } from 'vue-router'

import useLoading from '@/hooks/loading'
import useChatStore from '@/stores/chat'

import Editor from '@tinymce/tinymce-vue'

const emit = defineEmits(['onHandleResize', 'onHandlePushMessage'])

const route = useRoute()

const { loading, setLoading } = useLoading()
const { sendMessage } = useChatStore()

// 项目 ID
const projectId = computed(() => Number(route.params.project_id))

// 编辑器配置
const editorInit = reactive({
  menubar: false,
  width: '100%',
  height: '100%',
  resize: false,
  toolbar_mode: 'floating',
  statusbar: false
})

// 聊天内容
const content = ref('')
// 聊天框可见
const visible = ref(false)

/**
 * 发送消息
 */
const handleSendMessage = () => {
  // 验证表单
  if (!content.value) return
  if (!projectId.value) return
  // 提交表单
  setLoading(true)
  sendMessage(projectId.value, content.value)
    .then((res) => {
      content.value = ''
      emit('onHandlePushMessage', res)
    })
    .finally(() => {
      setLoading(false)
    })
}

/**
 * 显示编辑框
 */
const handleShowEditor = () => {
  visible.value = true
  emit('onHandleResize', 0.5)
}

/**
 * 编辑框失去焦点
 */
const handleBlur = () => {
  if (!content.value) {
    visible.value = false
    emit('onHandleResize', 0.9)
  }
}
</script>

<style lang="less">
.coresite-chat-area {
  height: 100%;
  justify-content: space-between;

  & > .arco-space-item:first-child {
    flex: 1 0 auto;
  }

  .tox-tinymce {
    border-top: 0;
    border-left: 0;
    border-right: 0;
    border-radius: 0;
    z-index: 0;
  }
}
</style>

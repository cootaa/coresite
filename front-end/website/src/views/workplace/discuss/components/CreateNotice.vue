<template>
  <a-space direction="vertical" fill>
    <a-textarea
      v-model="content"
      :auto-size="{ minRows: 5, maxRows: 8 }"
      :placeholder="$t('workplace.discuss.notice.create.text')"
    ></a-textarea>
    <a-row justify="end" align="center">
      <a-col flex="none">
        <a-button type="primary" :loading="loading" @click="handleSubmit">{{
          $t('workplace.discuss.notice.create.button')
        }}</a-button>
      </a-col>
    </a-row>
  </a-space>
</template>

<script lang="ts" setup>
import { ref } from 'vue'

import useLoading from '@/hooks/loading'
import useWorkspaceStore from '@/stores/workspace'

const props = defineProps<{
  groupId: number
}>()

const emit = defineEmits(['onHandleUpdate'])

const { createNotice } = useWorkspaceStore()

const { loading, setLoading } = useLoading(false)

const content = ref('')

const handleSubmit = () => {
  // 验证表单
  const group_id = props.groupId
  if (!group_id) return
  // 提交表单
  setLoading(true)
  createNotice({ group_id, content: content.value }).finally(() => {
    setLoading(false)
    emit('onHandleUpdate')
  })
}
</script>

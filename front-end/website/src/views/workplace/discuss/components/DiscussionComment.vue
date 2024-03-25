<template>
  <a-space direction="vertical" :size="0" fill>
    <a-divider style="margin-top: 30px" />
    <a-typography-title :heading="6">
      {{ $t('workplace.discuss.discussion.detail.comment') }}
    </a-typography-title>

    <a-list class="discussion-commemt" :bordered="false" :split="true">
      <!-- No Discussion -->
      <template #empty>
        <a-result :status="null" :title="$t('workplace.discuss.discussion.detail.comment.empty')" />
      </template>
      <!-- Discussion Item -->
      <a-list-item v-for="item in props.comments" :key="item.id">
        <a-space direction="vertical" fill>
          <!-- Comment Creator -->
          <a-row :gutter="12" align="center">
            <!-- Avatar -->
            <a-col flex="none"
              ><a-avatar :size="24"><img :src="item.comment_user.avatar" /></a-avatar
            ></a-col>
            <!-- Creator Name -->
            <a-col flex="none">
              <a-typography-text type="secondary">{{
                item.comment_user.nickname
              }}</a-typography-text>
            </a-col>
            <!-- Create Time -->
            <a-col flex="none">
              <a-typography-text type="secondary">{{ item.create_time }}</a-typography-text>
            </a-col>
          </a-row>
          <!-- Comment Content -->
          <a-typography-text class="discussion-commemt-content">{{
            item.comment
          }}</a-typography-text>
        </a-space>
      </a-list-item>
    </a-list>
    <a-space direction="vertical" :size="12" fill>
      <!-- Add Comment -->
      <a-typography-title :heading="6">
        {{ $t('workplace.discuss.discussion.detail.comment.add') }}
      </a-typography-title>
      <!-- Add Comment Textarea -->
      <a-textarea
        v-model="content"
        :auto-size="{ minRows: 4, maxRows: 5 }"
        :placeholder="$t('workplace.discuss.discussion.detail.comment.text')"
      />
      <!-- Add Comment Button -->
      <a-row justify="end" align="center">
        <a-col flex="none">
          <a-button type="primary" :loading="loading" @click="handleSubmit">{{
            $t('workplace.discuss.discussion.detail.comment.button')
          }}</a-button>
        </a-col>
      </a-row>
    </a-space>
  </a-space>
</template>

<script lang="ts" setup>
import { ref } from 'vue'

import { useI18n } from 'vue-i18n'
import useLoading from '@/hooks/loading'

import useWorkspaceStore from '@/stores/workspace'

import { Message } from '@arco-design/web-vue'

const props = defineProps<{
  groupId?: number
  discussionId?: number
  comments: ICommentItem[]
}>()

const emit = defineEmits(['onHandleUpdate'])

const { t } = useI18n()
const { loading, setLoading } = useLoading()
const { createComment } = useWorkspaceStore()

// 新评论
const content = ref('')

// 提交评论
const handleSubmit = () => {
  // 验证表单
  if (!props.discussionId) return
  if (!props.groupId) return
  if (!content.value)
    return Message.error(t('workplace.discuss.discussion.detail.comment.text.empty'))
  // 提交表单
  setLoading(true)
  createComment({
    group_id: props.groupId,
    discussion_id: props.discussionId,
    comment: content.value
  })
    .then((res) => {
      Message.success(res?.msg as string)
      content.value = ''
    })
    .finally(() => {
      setLoading(false)
      emit('onHandleUpdate')
    })
}
</script>

<style lang="less">
.discussion-commemt {
  .arco-list-item {
    padding: 32px !important;
  }

  .discussion-commemt-content {
    margin-left: 34px;
  }
}
</style>

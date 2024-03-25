<template>
  <a-scrollbar
    class="discussion-detail"
    style="max-height: 80vh; overflow: auto; padding: 24px"
    type="track"
    :key="new Date().getTime()"
  >
    <a-spin :loading="loading">
      <a-space v-if="detail" direction="vertical" fill>
        <a-typography :style="{ marginTop: '-30px' }">
          <!-- Title -->
          <a-typography-title :heading="1">
            {{ detail?.title }}
          </a-typography-title>
          <!-- Creator Info -->
          <a-row :gutter="12">
            <!-- Avatar -->
            <a-col flex="none"
              ><a-avatar :size="24"><img :src="detail?.creator.avatar" /></a-avatar
            ></a-col>
            <!-- Creator Name -->
            <a-col flex="none">
              <a-typography-text type="secondary">{{ detail?.creator.nickname }}</a-typography-text>
            </a-col>
            <!-- Create Time -->
            <a-col flex="none">
              <a-typography-text type="secondary">{{ detail?.create_time }}</a-typography-text>
            </a-col>
            <!-- Commemt Count -->
            <a-col flex="none">
              <a-typography-text type="secondary">
                <icon-message />
                {{ detail?.comments.length }}
              </a-typography-text>
            </a-col>
          </a-row>
          <!-- Content -->
          <a-typography-paragraph class="discussion-detail-content">
            <div v-html="detail?.content"></div>
          </a-typography-paragraph>
          <!-- Comments -->
          <DiscussionComment
            :group-id="detail?.group_id"
            :discussion-id="detail?.id"
            :comments="detail?.comments ?? []"
            @on-handle-update="handleUpdate"
          />
        </a-typography>
      </a-space>
    </a-spin>
  </a-scrollbar>
</template>

<script lang="ts" setup>
import { ref, watchEffect } from 'vue'

import useWorkspaceStore from '@/stores/workspace'
import useLoading from '@/hooks/loading'

import type { IGetDiscussionDetailResponse } from '../api/discussion'

import DiscussionComment from './DiscussionComment.vue'

const props = defineProps<{
  discussion_id?: number
}>()

const emit = defineEmits(['onHandleError'])

const { getDiscussionContent } = useWorkspaceStore()
const { loading, setLoading } = useLoading()

// 讨论详情
const detail = ref<IGetDiscussionDetailResponse>()

watchEffect(() => {
  if (props.discussion_id) getDetail()
})

const getDetail = () => {
  if (!props.discussion_id) return emit('onHandleError')
  setLoading(true)
  getDiscussionContent({
    discussion_id: props.discussion_id
  })
    .then((res) => {
      if (res) {
        detail.value = res
      } else {
        emit('onHandleError')
      }
    })
    .finally(() => {
      setLoading(false)
    })
}

const handleUpdate = () => {
  getDetail()
}
</script>

<style lang="less">
.discussion-detail {
  .discussion-detail-content {
    font-size: 16px;
    margin-top: 48px;
  }

  .arco-spin {
    width: 99%;
  }
}
</style>

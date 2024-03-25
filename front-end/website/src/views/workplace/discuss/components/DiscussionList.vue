<template>
  <a-list
    class="discussion-list"
    :bordered="false"
    :split="false"
    :hoverable="true"
    @reach-bottom="handleReachBottom"
  >
    <!-- No Discussion -->
    <template #empty>
      <a-result :status="null" :title="$t('workplace.discuss.discussion.empty')" />
    </template>
    <!-- Discussion Item -->
    <a-list-item
      v-for="item in props.discussionList"
      :key="item.id"
      @click="() => handleOpenDetail(item.id)"
    >
      <template #meta>
        <a-space direction="vertical" fill>
          <a-typography-title :heading="6">
            {{ item.title }}
          </a-typography-title>
          <a-space :size="12" fill>
            <a-typography-paragraph type="secondary" size="small">{{
              item.creator.nickname
            }}</a-typography-paragraph>
            <a-typography-paragraph type="secondary">
              <time>{{ item.update_time }}</time>
            </a-typography-paragraph>
          </a-space>
        </a-space>
      </template>
      <template #actions>
        <!-- Discussion Members -->
        <a-avatar-group :size="24" :max-count="5">
          <a-avatar><img :src="item.creator.avatar" /> </a-avatar>
          <a-avatar v-for="avatar in item.comment_user" :key="avatar.nickname">
            <img :src="avatar.avatar" />
          </a-avatar>
        </a-avatar-group>
        <!-- Discussion Count -->
        <a-space align="center">
          <a-typography-text type="secondary">
            <icon-message size="large" />
            {{ item.comment_count }}
          </a-typography-text>
        </a-space>
      </template>
    </a-list-item>
  </a-list>
  <!-- Discussion Detail -->
  <Modal
    v-model:visible="detailVisible"
    :title="$t('workplace.discuss.discussion.detail.title')"
    :modal-style="{ width: '960px', 'max-width': '90vw' }"
    @on-before-cancel="handleCloseDetail"
  >
    <DiscussionDetail :discussion_id="discussionId" @on-handle-error="handleOpenDetailFail" />
  </Modal>
</template>

<script lang="ts" setup>
import { ref } from 'vue'

import Modal from '@/layout/Modal.vue'
import DiscussionDetail from './DiscussionDetail.vue'

const props = defineProps<{
  discussionList: IDiscussionItem[]
}>()

const emit = defineEmits(['onHandleReachBottom'])

// 打开讨论详情
const discussionId = ref<number>()
const detailVisible = ref(false)
const handleOpenDetail = (discussion_id: number) => {
  detailVisible.value = true
  discussionId.value = discussion_id
}

// 打开详情失败
const handleOpenDetailFail = () => {
  if (!discussionId.value) return
  detailVisible.value = false
}
// 关闭详情
const handleCloseDetail = () => {
  discussionId.value = undefined
}

// 加载下一页
const handleReachBottom = () => {
  emit('onHandleReachBottom')
}
</script>

<style lang="less">
.discussion-list {
  background-color: var(--color-bg-2);

  .arco-list-item {
    cursor: pointer;
  }
}
</style>

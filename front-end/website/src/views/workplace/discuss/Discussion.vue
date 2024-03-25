<template>
  <a-spin :loading="loading">
    <a-space class="coresite-discussion" direction="vertical" :size="24" fill>
      <!-- Discussion Title -->
      <a-row class="coresite-discuss-title" justify="space-between" align="center">
        <a-col flex="none"><icon-message /> {{ $t('workplace.discuss.discussion.title') }}</a-col>
        <!-- Public Button -->
        <a-col flex="none">
          <a-tooltip :content="$t('workplace.discuss.discussion.new')" position="bottom">
            <a-button type="outline" size="small" @click="handleCreateDiscussion">
              <template #icon>
                <icon-plus />
              </template>
            </a-button>
          </a-tooltip>
        </a-col>
      </a-row>
      <!-- Discussion List -->
      <DiscussionList :discussion-list="discussionList" @on-handle-reach-bottom="getDiscussions" />
    </a-space>
  </a-spin>
  <CreateDiscussion
    v-model:visible="createDiscussionVisible"
    :group-id="groupId"
    @on-handle-update="handleUpdate"
  />
</template>

<script lang="ts" setup>
import { computed, nextTick, onMounted, ref } from 'vue'
import type { IGetDiscussionListResponse } from './api/discussion'

import { useRoute } from 'vue-router'

import useLoading from '@/hooks/loading'
import useWorkspaceStore from '@/stores/workspace'

import DiscussionList from './components/DiscussionList.vue'
import CreateDiscussion from './components/CreateDiscussion.vue'

const { loading, setLoading } = useLoading()

const route = useRoute()
const workspaceStore = useWorkspaceStore()

// 组织 ID
const groupId = computed(() => Number(route.params.group_id))
// 讨论列表
const discussionList = ref<IDiscussionItem[]>([])
// 讨论每页数量
const discussionPageSize = ref<number>(10)
// 讨论当前分页
const discussionCurrentPage = ref<number>(0)
// 讨论没有更多
const hasMore = ref<boolean>(true)
// 新建讨论是否可见
const createDiscussionVisible = ref<boolean>(false)

// onMounted(() => {
//   initGetDiscussion()
// })

// 初始化加载讨论
const initGetDiscussion = () => {
  discussionList.value = []
  discussionCurrentPage.value = 0
  hasMore.value = true
  getDiscussions()
}

// 触底加载
nextTick(() => {
  let preScrollTop = 0
  document.querySelector('.workplace-center')?.addEventListener('scroll', (e: Event) => {
    const { scrollTop } = e.target as HTMLElement
    if (scrollTop > preScrollTop) {
      getDiscussions()
    }
    preScrollTop = scrollTop
  })
})

/**
 * 获取讨论列表
 */
const getDiscussions = () => {
  if (!hasMore.value) return
  if (!groupId.value) return
  setLoading(true)
  discussionCurrentPage.value += 1
  workspaceStore
    .getDiscussions({
      group_id: groupId.value,
      page: discussionCurrentPage.value,
      size: discussionPageSize.value
    })
    .then((res) => {
      const { discussion_list } = res as IGetDiscussionListResponse
      if (discussion_list.length > 0) {
        discussionList.value.push(...discussion_list)
      } else {
        hasMore.value = false
      }
    })
    .finally(() => {
      setLoading(false)
    })
}

// 创建讨论
const handleCreateDiscussion = () => {
  createDiscussionVisible.value = true
}

// 更新回调
const handleUpdate = () => {
  createDiscussionVisible.value = false
  initGetDiscussion()
}
</script>

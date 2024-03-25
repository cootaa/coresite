<template>
  <a-spin :loading="loading">
    <a-space class="coresite-notice" direction="vertical" :size="12" fill>
      <!-- Notice Title -->
      <a-row class="coresite-discuss-title" justify="space-between" align="center">
        <a-col flex="none"><icon-bulb /> {{ $t('workplace.discuss.notice.title') }}</a-col>
        <!-- Public Button -->
        <a-col flex="none">
          <a-tooltip :content="$t('workplace.discuss.notice.new')" position="br">
            <a-button type="outline" size="small" @click="handleCreateNotice">
              <template #icon>
                <icon-plus />
              </template>
            </a-button>
          </a-tooltip>
        </a-col>
      </a-row>
      <!-- Notice List (2 items) -->
      <NoticeList
        :notice-list="unExpandNoticeList.slice(0, 2)"
        @on-handle-before-update="beforeUpdate"
        @on-handle-update="initNoticeList"
      />
      <!-- More Button -->
      <a-button v-if="unExpandNoticeList.length > 2" style="width: 100%" @click="handleMoreNotice"
        >More</a-button
      >
    </a-space>
  </a-spin>
  <!-- Create Notice -->
  <Modal v-model:visible="createNoticeModalVisible" :title="$t('workplace.discuss.notice.create')">
    <CreateNotice
      :group-id="groudId"
      :key="new Date().getTime()"
      @on-handle-update="handleCreateNoticeUpdate"
    />
  </Modal>
  <!-- More Notice -->
  <Modal v-model:visible="moreNoticeModalVisible" :title="$t('workplace.discuss.notice.title')">
    <NoticeList
      :loading="loading"
      :notice-list="noticeList"
      @on-handle-before-update="beforeUpdate"
      :max-height="500"
      @reach-bottom="loadMoreNoticeList"
      @on-handle-update="handleMoreNoticeAfterUpdate"
    />
  </Modal>
</template>

<script lang="ts" setup>
import { computed, onMounted, ref } from 'vue'
import { useRoute } from 'vue-router'
import type { IGetNoticesData, IGetNoticesResponse } from './api/notice'

import useWorkspaceStore from '@/stores/workspace'
import useLoading from '@/hooks/loading'

import NoticeList from './components/NoticeList.vue'
import Modal from '@/layout/Modal.vue'
import CreateNotice from './components/CreateNotice.vue'

const route = useRoute()
const workspaceStore = useWorkspaceStore()

const { loading, setLoading } = useLoading()

// 未展开公告列表
const unExpandNoticeList = ref<INoticeItem[]>([])
// 公告列表
const noticeList = ref<INoticeItem[]>([])
// 当前页数
const currentPage = ref(1)
// 每页展示数量
const pageSize = ref(5)
// 有无更多
const hasMore = ref(true)
// 组织 ID
const groudId = computed(() => Number(route.params.group_id))

onMounted(() => {
  initNoticeList()
})

/**
 * 获取公告列表
 */
const getNoticeList = async (data?: Omit<IGetNoticesData, 'group_id' | 'user_id'>) => {
  if (!groudId.value) return null
  return workspaceStore.getNoticeList({
    ...data,
    group_id: groudId.value
  })
}

/**
 * 初次进入获取 2 条公告
 */
const initNoticeList = () => {
  setLoading(true)
  getNoticeList({
    size: 3, // 每次获取 3 条，用来判断有没有更多的公告
    page: 1
  })
    .then((res) => {
      unExpandNoticeList.value = res?.notice_list ?? []
    })
    .finally(() => {
      setLoading(false)
    })
}

/**
 * 点击 MORE 获取更多公告
 */
const initGetMoreNoticeList = () => {
  noticeList.value = []
  currentPage.value = 0
  hasMore.value = true
  loadMoreNoticeList()
}

/**
 * 获取更多公告
 */
const loadMoreNoticeList = () => {
  if (!hasMore.value) return
  setLoading(true)
  currentPage.value += 1
  getNoticeList({
    size: pageSize.value,
    page: currentPage.value
  })
    .then((res) => {
      const { notice_list } = res as IGetNoticesResponse
      if (notice_list.length > 0) {
        noticeList.value.push(...notice_list)
        if (notice_list.length < pageSize.value) {
          hasMore.value = false
        }
      } else {
        hasMore.value = false
      }
    })
    .finally(() => {
      setLoading(false)
    })
}

// 创建公告
const createNoticeModalVisible = ref(false)
const handleCreateNotice = () => {
  createNoticeModalVisible.value = true
}
const handleCreateNoticeUpdate = () => {
  createNoticeModalVisible.value = false
  initNoticeList()
}

// 更新前
const beforeUpdate = () => {
  setLoading(true)
}

// 公告列表是否可见
const moreNoticeModalVisible = ref(false)
const handleMoreNotice = () => {
  moreNoticeModalVisible.value = true
  initGetMoreNoticeList()
}
// 公告列表更新后执行
const handleMoreNoticeAfterUpdate = () => {
  initGetMoreNoticeList()
  initNoticeList()
}
</script>

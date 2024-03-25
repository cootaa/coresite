<template>
  <a-list class="notice-list" :bordered="false" :split="false">
    <!-- No Notice -->
    <template #empty>
      <a-result :status="null" :subtitle="$t('workplace.discuss.notice.empty')" />
    </template>
    <!-- Notice Item -->
    <a-list-item v-for="item in props.noticeList" :key="item.id">
      <a-card>
        <!-- Notice Action -->
        <template #actions>
          <!-- Notice Delete -->
          <a-popconfirm
            :content="$t('workplace.discuss.notice.delete.ps')"
            position="br"
            @ok="() => handleDelete(item.id, item.group_id)"
          >
            <a-button size="small">
              <template #icon>
                <icon-delete />
              </template>
            </a-button>
          </a-popconfirm>
        </template>
        <a-card-meta>
          <!-- Notice Content -->
          <template #description>{{ item.content }}</template>
          <!-- Notice Time -->
          <template #avatar>
            <a-typography-text>{{ item.update_time }}</a-typography-text>
          </template>
        </a-card-meta>
      </a-card>
    </a-list-item>
  </a-list>
</template>

<script lang="ts" setup>
import useWorkspaceStore from '@/stores/workspace'
import { Message } from '@arco-design/web-vue'

const props = defineProps<{
  noticeList: INoticeItem[]
}>()

const emit = defineEmits(['onHandleBeforeUpdate', 'onHandleUpdate'])

const { delNotice } = useWorkspaceStore()

// 删除公告
const handleDelete = (notice_id: number, group_id: number) => {
  // 验证表单
  if (!notice_id || !group_id) return
  // 提交表单
  emit('onHandleBeforeUpdate')
  delNotice({
    notice_id,
    group_id
  })
    .then((res) => {
      Message.success(res?.data as string)
    })
    .finally(() => {
      emit('onHandleUpdate')
    })
}
</script>

<style lang="less">
.notice-list {
  .arco-list-item {
    padding: 13px 0 !important;
  }

  .arco-card-meta-description {
    word-break: break-all;
  }
}
</style>

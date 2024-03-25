<template>
  <a-list class="project-member-list" :bordered="false">
    <a-list-item v-for="item in props.projectMembers" :key="item.user_id">
      <a-list-item-meta>
        <!-- User Nickname -->
        <template #title>{{ item.user.nickname }}</template>
        <!-- User Avatar -->
        <template #avatar>
          <a-image
            width="32"
            height="32"
            :src="item.user.avatar || UserDefaultAvatar"
            :preview="false"
          />
        </template>
      </a-list-item-meta>
      <!-- User Actions -->
      <template #actions>
        <a-popconfirm
          :content="$t('workplace.project.setting.deleteMember.ps')"
          @ok="() => handleDeleteMember(item.user_id)"
        >
          <icon-delete />
        </a-popconfirm>
      </template>
    </a-list-item>
  </a-list>
</template>

<script lang="ts" setup>
import { UserDefaultAvatar } from '@/config/common'
import type { IMemberType } from '@/views/group/api/setting'

const props = defineProps<{
  groupId: number
  projectId: number
  projectMembers: IMemberType[]
}>()
const emit = defineEmits(['handleDeleteMember'])

/**
 * 移除成员
 */
const handleDeleteMember = (user_id: number) => {
  emit('handleDeleteMember', user_id)
}
</script>

<style lang="less">
.project-member-list {
  .arco-list-item {
    padding: 12px 0 !important;
  }
}
</style>

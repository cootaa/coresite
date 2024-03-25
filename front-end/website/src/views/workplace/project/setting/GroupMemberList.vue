<template>
  <a-drawer :title="$t('workplace.project.setting.userSetting.title')" :footer="false">
    <a-list :bordered="false">
      <a-list-item
        v-for="item in props.groupMembers"
        :key="item.user_id"
        :class="{ 'opacity-60': isMemberInProject(item.user_id) }"
      >
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
        <template #actions v-if="!isMemberInProject(item.user_id)">
          <a-popconfirm
            :content="$t('workplace.project.setting.addMember.ps')"
            @ok="() => handleAddProjectMember(item.user_id)"
          >
            <icon-plus />
          </a-popconfirm>
        </template>
      </a-list-item>
    </a-list>
  </a-drawer>
</template>

<script lang="ts" setup>
import { UserDefaultAvatar } from '@/config/common'
import type { IMemberType } from '@/views/group/api/setting'

const props = defineProps<{
  projectMembers: IMemberType[]
  groupMembers: IMemberType[]
}>()

const emit = defineEmits(['handleAddProjectMember'])

/**
 * 判断该成员是否为当前项目成员
 */
const isMemberInProject = (userId: number) => {
  const member = props.projectMembers.find((item) => item.user_id === userId)
  if (member) {
    return true
  }
  return false
}

/**
 * 添加成员
 */
const handleAddProjectMember = (userId: number) => {
  emit('handleAddProjectMember', userId)
}
</script>

<template>
  <a-space class="coresite-member" direction="vertical" :size="24" fill>
    <!-- Member Title -->
    <a-row class="coresite-discuss-title" justify="space-between" align="center">
      <a-col flex="none"><icon-user-group /> {{ $t('workplace.discuss.member.title') }}</a-col>
    </a-row>
    <!-- Member List -->
    <a-space align="center" wrap fill>
      <a-tooltip
        v-for="item in members"
        :key="item.nickname"
        :content="item.nickname"
        position="bottom"
      >
        <a-avatar :size="32">
          <img :src="item.avatar" />
        </a-avatar>
      </a-tooltip>
    </a-space>
  </a-space>
</template>

<script lang="ts" setup>
import { computed } from 'vue'
import { useRoute } from 'vue-router'

import useGroupStore from '@/stores/group'

const route = useRoute()
const groupStore = useGroupStore()

// 当前组织 ID
const groupId = computed(() => Number(route.params.group_id))
// 获取成员列表
const members = computed(() => groupStore.getGroupById(groupId.value)?.members)
</script>

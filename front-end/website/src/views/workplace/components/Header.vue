<template>
  <Navbar class="workplace-header">
    <template #LeftIcons>
      <a-space align="center" size="medium">
        <!-- Group Icon -->
        <a-image width="24" height="24" :src="currentGroup?.icon" :preview="false" />
        <!-- Group name -->
        <div class="workplace-header-title">{{ currentGroup?.name }}</div>
        <!-- Group Setting -->
        <ButtonGroupSetting />
      </a-space>
    </template>
  </Navbar>
</template>

<script lang="ts" setup>
import { computed } from 'vue'

import useGroupStore from '@/stores/group'

import { useRoute } from 'vue-router'

import Navbar from '@/components/navbar/index.vue'

import ButtonGroupSetting from '@/components/button/GroupSetting.vue'

const route = useRoute()
const groupStore = useGroupStore()

const currentGroupId = computed(() => route.params.group_id)
const currentGroup = computed(() => groupStore.getGroupById(Number(currentGroupId.value as string)))
</script>

<style lang="less" scoped>
.workplace-header {
  background-color: var(--color-bg-2);

  &-title {
    font-size: 1.4rem;
    font-weight: bold;
    color: var(--color-text-1);
    line-height: 1;
  }
}
</style>

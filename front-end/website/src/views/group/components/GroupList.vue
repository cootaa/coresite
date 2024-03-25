<template>
  <a-space class="group-list-content" direction="vertical" size="large" fill>
    <!-- No Group Title -->
    <span class="group-title">{{ $t('group.list.title') }}</span>
    <a-scrollbar style="height: 70vh; overflow: auto; padding: 1px">
      <a-grid :row-gap="12" :col-gap="12" :cols="{ xs: 1, sm: 2, md: 2, lg: 2, xl: 2, xxl: 3 }">
        <GroupItem v-for="item in props.list" :key="item.group_id" :item="item" />
      </a-grid>
    </a-scrollbar>
  </a-space>
</template>

<script lang="ts" setup>
import type { IGroupItem } from '../api'
import GroupItem from './GroupItem.vue'

const props = defineProps<{
  list: IGroupItem[]
}>()
</script>

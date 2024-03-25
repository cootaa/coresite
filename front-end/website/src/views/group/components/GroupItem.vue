<template>
  <a-grid-item>
    <!-- <a-space class="group-button" align="center" fill @click="handleClick">
    </a-space> -->
    <a-row class="group-button" :gutter="12" align="center" @click="handleClick">
      <a-col flex="none">
        <a-image width="48" height="48" :src="props.item.icon" :preview="false" />
      </a-col>
      <a-col flex="auto">
        <a-row align="center" justify="space-between">
          <a-col flex="none">
            <span class="group-button-title">{{ props.item.name }}</span>
          </a-col>
          <a-col flex="none">
            <a-avatar-group :max-count="3" :size="24">
              <a-avatar v-for="member in props.item.members" :key="member.nickname">
                <img :src="member.avatar" />
              </a-avatar>
            </a-avatar-group>
          </a-col>
        </a-row>
      </a-col>
    </a-row>
  </a-grid-item>
</template>

<script lang="ts" setup>
import { useRouter } from 'vue-router'

import type { IGroupItem } from '../api'

const props = defineProps<{
  item: IGroupItem
}>()

const router = useRouter()

/**
 * 单击跳转页面到指定组织页面
 */
const handleClick = () => {
  router.push({ name: 'workplace', params: { group_id: props.item.group_id } })
}
</script>

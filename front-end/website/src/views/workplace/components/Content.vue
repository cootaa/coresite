<template>
  <a-layout-content class="workplace-center">
    <!-- Project opened -->
    <router-view v-slot="{ Component, route }" v-if="currentProject">
      <transition name="fade" mode="out-in" appear>
        <component :is="Component" :key="route.fullPath" />
      </transition>
    </router-view>
    <!-- No open project -->
    <Discuss v-else />
  </a-layout-content>
</template>

<script lang="ts" setup>
import { computed } from 'vue'
import { useRoute } from 'vue-router'
import Discuss from '../discuss/index.vue'

const route = useRoute()

// 当前打开的项目
const currentProject = computed(() => route.params.project_id)
</script>

<style lang="less">
.workplace-center {
  position: relative;
}
</style>

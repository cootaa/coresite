<template>
  <router-view v-slot="{ Component, route }">
    <transition name="fade" mode="out-in" appear>
      <div class="center">
        <component :is="Component" :key="route.fullPath" />
      </div>
    </transition>
  </router-view>
</template>

<script lang="ts">
/**
 * 水平垂直居中布局
 */

export default {
  name: 'CenterLayout'
}
</script>

<style lang="less">
.center {
  display: flex;
  align-items: center;
  justify-content: center;
  min-height: 100vh;
}
</style>

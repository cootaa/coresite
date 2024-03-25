<template>
  <a-layout class="height-fill">
    <!-- Header -->
    <a-layout-header>
      <slot name="Header">
        <Navbar :no-border="props.noBorder" :no-user="props.noUser" :no-back="props.noBack" />
      </slot>
    </a-layout-header>
    <!-- Content -->
    <a-layout-content :class="{ 'center-layout': center }">
      <slot>
        <router-view v-slot="{ Component, route }">
          <transition name="fade" mode="out-in" appear>
            <component :is="Component" :key="route.fullPath" />
          </transition>
        </router-view>
      </slot>
    </a-layout-content>
    <!-- Footer -->
    <a-layout-footer>
      <slot name="Footer">
        <Footer />
      </slot>
    </a-layout-footer>
  </a-layout>
</template>

<script lang="ts">
/**
 * 经典布局
 */

export default {
  name: 'Classical'
}
</script>

<script lang="ts" setup>
import Navbar from '@/components/navbar/index.vue'
import Footer from '@/views/home/components/Footer.vue'

const props = defineProps<{
  noUser?: Boolean // 不带 User 按钮
  noBorder?: Boolean // 不带下划分割线
  center?: Boolean // Main 居中布局
  noBack?: Boolean // 不带返回按钮
}>()
</script>

<style>
.center-layout {
  display: flex;
  align-items: center;
  justify-content: center;
}
</style>

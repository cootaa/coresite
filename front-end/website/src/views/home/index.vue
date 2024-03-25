<template>
  <a-layout class="home" style="min-height: 100vh">
    <a-layout-header><Navbar /></a-layout-header>
    <a-layout-content><Cover /></a-layout-content>
    <a-layout-footer><Footer /></a-layout-footer>
  </a-layout>
</template>

<script lang="ts" setup>
import Navbar from './components/Navbar.vue'
import Cover from './components/Cover.vue'
import Footer from './components/Footer.vue'
</script>

<script lang="ts">
export default {
  name: 'Home'
}
</script>

<style scoped>
.home {
  text-align: center;
}
.home :deep(.arco-layout-content) {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
}
</style>

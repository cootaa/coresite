<template>
  <a-row class="coresite-discuss" :gutter="48">
    <a-col :span="16">
      <Discussion />
    </a-col>
    <a-col :span="8">
      <a-space direction="vertical" :size="32" fill>
        <Notice />
        <Member />
      </a-space>
    </a-col>
  </a-row>
</template>

<script lang="ts" setup>
import Discussion from './Discussion.vue'
import Notice from './Notice.vue'
import Member from './Member.vue'
</script>

<style lang="less">
.coresite-discuss {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  padding: 24px;
  margin-left: 0 !important;
  margin-right: 0 !important;

  &-title {
    font-size: 18px;
    font-weight: bold;
  }
}
</style>

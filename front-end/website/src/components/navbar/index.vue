<template>
  <a-row
    class="coresite-navbar"
    :class="{ border: !noBorder }"
    align="center"
    justify="space-between"
  >
    <!-- Left Icons -->
    <a-col flex="none">
      <slot name="LeftIcons">
        <ButtonGoBack v-if="!props.noBack" />
      </slot>
    </a-col>
    <!-- Right Icons -->
    <a-col flex="auto">
      <a-row :gutter="20" justify="end">
        <slot name="RightIcons">
          <!-- Language -->
          <a-col flex="none">
            <ButtonLocale />
          </a-col>
          <!-- Theme -->
          <a-col flex="none">
            <ButtonTheme />
          </a-col>
          <!-- User -->
          <a-col flex="none" v-if="!props.noUser">
            <ButtonUser />
          </a-col>
        </slot>
      </a-row>
    </a-col>
  </a-row>
</template>

<script lang="ts" setup>
import ButtonGoBack from '@/components/button/GoBack.vue'
import ButtonLocale from '@/components/button/Locale.vue'
import ButtonTheme from '@/components/button/Theme.vue'
import ButtonUser from '@/components/button/User.vue'

const props = defineProps<{
  noUser?: Boolean // 不带 User 按钮
  noBorder?: Boolean // 不带下划分割线
  noBack?: Boolean // 不带返回按钮
}>()
</script>

<style lang="less" scoped>
.coresite-navbar {
  padding: 12px;

  &.border {
    border-bottom: 1px solid var(--color-neutral-2);
  }
}
</style>

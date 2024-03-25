<template>
  <a-list class="coresite-chat-list" :split="false" :bordered="false" :max-height="'100%'">
    <template #empty></template>
    <a-list-item v-for="item in props.messages" :key="item.id">
      <a-space direction="vertical" fill>
        <a-row justify="space-between" align="center">
          <!-- Message Creator -->
          <a-col flex="none">
            <a-space align="center">
              <a-avatar :size="24"><img :src="item.user.avatar" /></a-avatar>
              <span>{{ item.user.nickname }}</span>
            </a-space>
          </a-col>
          <!-- Message Time -->
          <a-col flex="none">
            <a-typography-text type="secondary">{{
              formatTime(item.create_time)
            }}</a-typography-text>
          </a-col>
        </a-row>
        <!-- Message Content -->
        <div class="coresite-chat-list-message" v-html="item.message"></div>
      </a-space>
    </a-list-item>
  </a-list>
</template>

<script lang="ts" setup>
import moment from 'moment'
import { useI18n } from 'vue-i18n'

const { locale } = useI18n()

const props = defineProps<{
  messages: IMessageItem[]
}>()

const formatTime = (time: string) => {
  const a = moment(time).locale(locale.value.toLocaleLowerCase())
  return a.fromNow()
}
</script>

<style lang="less">
.coresite-chat-list {
  position: absolute;
  width: 100%;
  height: 100%;

  .arco-scrollbar {
    height: 100%;
  }

  .coresite-chat-list-message {
    margin-left: 32px;
    padding: 12px;
    border-radius: 8px;
    background-color: var(--color-bg-3);
  }
}
</style>

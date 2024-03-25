<template>
  <a-config-provider :locale="locale">
    <router-view />
  </a-config-provider>
</template>

<script setup lang="ts">
import { computed, watch } from 'vue'
import enUS from '@arco-design/web-vue/es/locale/lang/en-us'
import zhCN from '@arco-design/web-vue/es/locale/lang/zh-cn'
import useLocale from '@/hooks/locale'
import useTheme from '@/hooks/theme'
import useUserStore from './stores/user'

const userStore = useUserStore()

// 国际化
const { currentLocale, changeLocale } = useLocale()
const locale = computed(() => {
  switch (currentLocale.value) {
    case 'zh-CN':
      return zhCN
    case 'en-US':
      return enUS
    default:
      return enUS
  }
})
// 监听用户登陆变化更改语言
watch(
  () => userStore.setting?.lang,
  (lang) => {
    if (lang) {
      changeLocale(lang)
    }
  }
)
// 暗黑模式
const { initTheme } = useTheme()
initTheme()
</script>

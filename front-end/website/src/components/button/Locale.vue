<template>
  <a-dropdown @select="handleSelect">
    <a-tooltip :content="$t('settings.language')">
      <a-button type="text">
        <template #icon>
          <icon-language :size="20" />
        </template>
      </a-button>
    </a-tooltip>
    <template #content>
      <a-doption v-for="item in locales" :key="item.value" :value="item.value">
        <template #icon>
          <icon-check v-show="item.value === currentLocale" />
        </template>
        {{ item.label }}
      </a-doption>
    </template>
  </a-dropdown>
</template>

<script lang="ts" setup>
import { reactive } from 'vue'
import { LOCALE_OPTIONS } from '@/i18n'

import useLocale from '@/hooks/locale'
import useUserStore from '@/stores/user'

const userStore = useUserStore()

const { currentLocale, changeLocale } = useLocale()

// 可选语言列表
const locales = reactive([...LOCALE_OPTIONS])

// 提交更改账号语言到后端
const handleSelect = (val: string) => {
  // 更改本地数据
  changeLocale(val)
  // 提交表单
  userStore.updateUserData({
    lang: val
  })
}
</script>

<script lang="ts">
/**
 * 语言切换按钮
 */
export default {
  name: 'ButtonLocale'
}
</script>

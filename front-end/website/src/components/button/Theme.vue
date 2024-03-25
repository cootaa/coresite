<template>
  <a-tooltip :content="$t('settings.theme')">
    <a-button type="text" @click="handleClick">
      <template #icon>
        <icon-moon-fill v-if="currentTheme === 'dark'" :size="20" />
        <icon-sun-fill v-else :size="20" />
      </template>
    </a-button>
  </a-tooltip>
</template>

<script lang="ts" setup>
import useTheme from '@/hooks/theme'
import useUserStore from '@/stores/user'

// Theme
const { currentTheme, toggleTheme } = useTheme()

const userStore = useUserStore()

// 提交更改账号语言到后端
const handleClick = () => {
  // 更改本地数据
  toggleTheme()
  // 提交表单
  userStore.updateUserData({
    theme: currentTheme.value
  })
}
</script>

<script lang="ts">
/**
 * 主题切换按钮
 */
export default {
  name: 'ButtonTheme'
}
</script>

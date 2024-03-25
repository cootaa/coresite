<template>
  <a-tooltip :content="$t('settings.goback')">
    <a-button @click="handleClick" size="large">
      <template #icon>
        <icon-left />
      </template>
    </a-button>
  </a-tooltip>
</template>

<script lang="ts" setup>
import { useRouter } from 'vue-router'

const router = useRouter()

const handleClick = () => {
  router.push({ name: 'home' })
}
</script>

<template>
  <a-space class="folder-list-create" align="center">
    <!-- File Name -->
    <a-input
      v-model="name"
      :placeholder="props.placeholder"
      @keypress.enter="handleSubmit"
    ></a-input>
    <!-- Save -->
    <a-button type="primary" @click="handleSubmit">
      <template #icon>
        <icon-check />
      </template>
    </a-button>
    <!-- Exit -->
    <a-button @click="handleClose">
      <template #icon>
        <icon-close />
      </template>
    </a-button>
  </a-space>
</template>

<script lang="ts" setup>
import { ref } from 'vue'

const props = defineProps<{
  defaultValue?: string
  id?: number
  folderId?: number
  placeholder: string
}>()

const emit = defineEmits(['onHandleSubmit', 'onHandleClose'])

// 文件名称
const name = ref<string>(props.defaultValue as string)

/**
 * 确认更名
 */
const handleSubmit = (e: Event) => {
  e.preventDefault()
  if (name.value && name.value !== props.defaultValue) {
    emit('onHandleSubmit', props.id, name.value, props.folderId)
  } else {
    emit('onHandleClose')
  }
}
/**
 * 取消更名
 */
const handleClose = (e: Event) => {
  e.preventDefault()
  emit('onHandleClose')
}
</script>

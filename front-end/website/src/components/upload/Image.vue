<template>
  <a-upload
    :fileList="file ? [file] : []"
    :show-file-list="false"
    :custom-request="customRequest"
    accept=".png,.jpg"
  >
    <template #upload-button>
      <div
        :class="`arco-upload-list-item${
          file && file.status === 'error' ? ' arco-upload-list-item-error' : ''
        }`"
      >
        <div class="arco-upload-list-picture custom-upload-avatar" v-if="file && file.url">
          <img :src="file.url" />
          <div class="arco-upload-list-picture-mask">
            <IconEdit />
          </div>
          <a-progress
            v-if="file.status === 'uploading' && (file.percent ?? 0) < 100"
            :percent="file.percent"
            type="circle"
            size="mini"
            :style="{
              position: 'absolute',
              left: '50%',
              top: '50%',
              transform: 'translateX(-50%) translateY(-50%)'
            }"
          />
        </div>
        <div class="arco-upload-picture-card" v-else>
          <div class="arco-upload-picture-card-text">
            <IconPlus />
            <div style="margin-top: 10px; font-weight: 600">Upload</div>
          </div>
        </div>
      </div>
    </template>
  </a-upload>
</template>

<script lang="ts" setup>
import { ref, watch, withDefaults } from 'vue'
import { useI18n } from 'vue-i18n'
import { Message, type FileItem, type RequestOption } from '@arco-design/web-vue'
import { EUploadType, uploadImage } from './api'

const props = withDefaults(
  defineProps<{
    type?: EUploadType
    defaultUrl?: string
  }>(),
  {
    type: EUploadType.AVATAR
  }
)
const emit = defineEmits(['update:imageUrl'])

const { t } = useI18n()

// 图片文件
const file = ref<FileItem>()

// 监听是否传入默认图片，有则展示
watch(
  () => props.defaultUrl,
  (val) => {
    if (val) {
      file.value = {
        uid: '-1',
        name: 'default',
        status: 'done',
        url: props.defaultUrl
      }
    }
  }
)

const customRequest = (option: RequestOption) => {
  const { fileItem } = option
  if (fileItem.file) {
    uploadImage({
      type: props.type,
      file: fileItem.file
    })
      .then((res) => {
        const { code, data, msg } = res.data
        if (code === 200) {
          file.value = fileItem
          emit('update:imageUrl', data.url)
          Message.success(t('upload.success'))
        } else {
          throw new Error(msg)
        }
      })
      .catch((err) => {
        Message.error(err.message)
      })
  }
}
</script>

<style scoped>
.arco-upload-list-item {
  margin-top: 0;
}
</style>

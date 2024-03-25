<template>
  <a-space direction="vertical" size="large" fill>
    <!-- Upload -->
    <a-row align="center">
      <a-col :span="8">
        <UploadImage @update:image-url="changeImageUrl" />
      </a-col>
      <a-col :span="16" class="group-ps">
        {{ $t('group.create.upload.ps') }}
      </a-col>
    </a-row>
    <!-- Name -->
    <a-input size="large" :placeholder="$t('group.create.input.name')" v-model="name" />
    <!-- Submit -->
    <a-button type="primary" size="large" @click="handleSubmit" :loading="loading">{{
      $t('group.create.submit')
    }}</a-button>
  </a-space>
</template>

<script lang="ts" setup>
import { ref } from 'vue'
import { useI18n } from 'vue-i18n'

import useLoading from '@/hooks/loading'
import useUserStore from '@/stores/user'
import useGroupStore from '@/stores/group'

import UploadImage from '@/components/upload/Image.vue'
import { Message } from '@arco-design/web-vue'

const { loading, setLoading } = useLoading(false)
const { t } = useI18n()

const userStore = useUserStore()
const groupStore = useGroupStore()

const imageUrl = ref('')
const name = ref('')

const changeImageUrl = (url: string) => {
  imageUrl.value = url
}

const handleSubmit = () => {
  // 判断是否登录
  if (!userStore.user_id) {
    return Message.error(t('group.create.noimage'))
  }
  // 判断表单内容
  if (imageUrl.value === '') {
    return Message.error(t('group.create.noimage'))
  }
  if (name.value === '') {
    return Message.error(t('group.create.noname'))
  }
  // 提交表单
  setLoading(true)
  groupStore
    .create({
      creator_id: userStore.user_id as number,
      name: name.value,
      icon: imageUrl.value
    })
    .then(() => {
      Message.success(t('group.create.success'))
    })
    .catch((res) => {
      Message.error(res.message)
    })
    .finally(() => {
      setTimeout(() => {
        window.location.reload()
      }, 1000)
    })
}
</script>

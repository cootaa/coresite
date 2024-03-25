<template>
  <a-space class="register" align="center" direction="vertical" :size="16" fill>
    <!-- Logo -->
    <a-typography class="text-center">
      <icon-logo class="register-logo" />
      <a-typography-title class="register-title" :heading="4">{{
        $t('signup.title')
      }}</a-typography-title>
    </a-typography>
    <!-- Form -->
    <a-form
      :model="form"
      @submit="handleSubmit"
      :style="commonStyle"
      :label-col-props="{ span: 0, offset: 0 }"
      :wrapper-col-props="{ span: 24, offset: 0 }"
      :rules="rules"
    >
      <a-form-item field="email">
        <a-input v-model="form.username" :placeholder="$t('signup.form.emal')" allow-clear />
      </a-form-item>
      <a-form-item field="password">
        <a-input-password
          v-model="form.password"
          :placeholder="$t('signup.form.password')"
          allow-clear
        />
      </a-form-item>
      <a-form-item field="repassword">
        <a-input-password
          v-model="form.repassword"
          :placeholder="$t('signup.form.repassword')"
          allow-clear
        />
      </a-form-item>
      <a-form-item field="nickname">
        <a-input v-model="form.nickname" :placeholder="$t('signup.form.nickname')" allow-clear />
      </a-form-item>
      <a-form-item>
        <a-button class="width-100" html-type="submit" type="primary" :loading="loading">{{
          $t('signup.button')
        }}</a-button>
      </a-form-item>
      <a-form-item>
        <div class="register-ps text-center width-100">
          {{ $t('signup.ps.unit') }}
          <router-link :to="{ name: 'login' }">{{ $t('signup.ps.link') }}</router-link>
        </div>
      </a-form-item>
    </a-form>
  </a-space>
</template>

<script lang="ts" setup>
import { reactive } from 'vue'
import { useI18n } from 'vue-i18n'

import { useRouter } from 'vue-router'
import useLoading from '@/hooks/loading'
import useUserStore from '@/stores/user'

import type { FieldRule } from '@arco-design/web-vue/es/form'

import IconLogo from '@/components/icons/Logo.vue'
import type { IRegisterData } from './api'
import Message from '@arco-design/web-vue/es/message'

const { t } = useI18n()
const { loading, setLoading } = useLoading()

const router = useRouter()
const userStore = useUserStore()

// 表单数据
const form = reactive({
  username: '',
  password: '',
  repassword: '',
  nickname: ''
})

// 表单验证规则
const rules: Record<string, FieldRule | FieldRule[]> = {
  username: [{ required: true }, { type: 'email', message: t('signup.form.emal.unmatch') }],
  password: { required: true },
  repassword: [
    { required: true },
    {
      validator: (value, cb) => {
        if (value !== form.password) {
          cb(t('signup.form.repassword.unmatch'))
        }
      }
    }
  ],
  nickname: { required: true }
}

// 公共样式
const commonStyle = {
  width: '320px',
  textAlign: 'center'
}

// 注册提交
const handleSubmit = async ({ errors, values }: TFormSubmitData) => {
  if (loading.value) return
  if (!errors) {
    try {
      setLoading(true)
      const msg = await userStore.register(values as IRegisterData)
      router.push({ name: 'login' })
      Message.success(msg)
    } catch (error) {
      Message.error((error as Error).message)
    } finally {
      setLoading(false)
    }
  }
}
</script>

<script lang="ts">
export default {
  name: 'Login'
}
</script>

<style lang="less" scoped>
.register {
  &-logo {
    width: 48px;
    height: 48px;
  }

  &-title {
    margin: 1rem 0;
  }

  &-ps {
    a {
      margin-left: 0.5rem;
      color: rgb(var(--primary-6));

      &:hover,
      &:focus,
      &:active {
        color: rgb(var(--primary-4));
      }
    }
  }
}
</style>

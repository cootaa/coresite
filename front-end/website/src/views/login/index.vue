<template>
  <a-space class="login" align="center" direction="vertical" :size="16" fill>
    <!-- Logo -->
    <a-typography class="text-center">
      <icon-logo class="login-logo" />
      <a-typography-title class="login-title" :heading="4">{{
        $t('signin.title')
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
      <a-form-item field="username">
        <a-input v-model="form.username" :placeholder="$t('signin.form.emal')" allow-clear />
      </a-form-item>
      <a-form-item field="password">
        <a-input-password
          v-model="form.password"
          :placeholder="$t('signin.form.password')"
          allow-clear
        />
      </a-form-item>
      <a-form-item>
        <a-button class="width-100" html-type="submit" type="primary" :loading="loading">{{
          $t('signin.button')
        }}</a-button>
      </a-form-item>
      <a-form-item>
        <div class="login-ps text-center width-100">
          {{ $t('signin.ps.unit') }}
          <router-link :to="{ name: 'register' }">{{ $t('signin.ps.link') }}</router-link>
        </div>
      </a-form-item>
    </a-form>
  </a-space>
</template>

<script lang="ts" setup>
import { reactive } from 'vue'
import { useI18n } from 'vue-i18n'
import { useRouter } from 'vue-router'
import { Message } from '@arco-design/web-vue'

import useUserStore from '@/stores/user'
import useLoading from '@/hooks/loading'

import type { FieldRule } from '@arco-design/web-vue/es/form'

import IconLogo from '@/components/icons/Logo.vue'
import type { ILoginData } from './api/login'

const { t } = useI18n()
const userStore = useUserStore()
const router = useRouter()
const { loading, setLoading } = useLoading(false)

const form = reactive({
  username: '',
  password: ''
})

const commonStyle = {
  width: '320px',
  textAlign: 'center'
}

const rules: Record<string, FieldRule | FieldRule[]> = {
  username: [{ required: true }, { type: 'email', message: t('signin.form.emal.unmatch') }],
  password: { required: true }
}

const handleSubmit = async ({ errors, values }: TFormSubmitData) => {
  if (loading.value) return
  if (!errors) {
    try {
      setLoading(true)
      await userStore.login(values as ILoginData)
      router.push({ name: 'groupList' })
      Message.success(t('signin.success.title'))
    } catch (error) {
      Message.error(t('signin.error.title'))
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
.login {
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

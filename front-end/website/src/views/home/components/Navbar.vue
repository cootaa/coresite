<template>
  <div class="home-navbar">
    <a-page-header :show-back="false">
      <template #title>
        <a-space size="medium">
          <IconLogo />
          <span class="site-title">{{ $t('home.site.title') }}</span>
          <a-tag size="small">
            <template #icon>
              <icon-check-circle-fill />
            </template>
            Beta 1.0
          </a-tag>
        </a-space>
      </template>
      <template #extra>
        <a-space size="large">
          <!-- Language -->
          <ButtonLocale />
          <!-- Theme -->
          <ButtonTheme />
          <!-- Sign In -->
          <a-button type="primary" @click="handleSignin">
            {{ $t('home.navbar.button.signin') }}
          </a-button>
          <!-- Sign Up -->
          <a-button @click="handleSignup">
            {{ $t('home.navbar.button.signup') }}
          </a-button>
        </a-space>
      </template>
    </a-page-header>
  </div>
</template>

<script lang="ts" setup>
import { useRouter } from 'vue-router'

import { isLogin } from '@/utils/token'

import IconLogo from '@/components/icons/Logo.vue'
import ButtonLocale from '@/components/button/Locale.vue'
import ButtonTheme from '@/components/button/Theme.vue'

const router = useRouter()

// SignUp
const handleSignup = () => {
  router.push({ name: 'register' })
}

// SignIn
const handleSignin = () => {
  if (isLogin()) {
    router.push({ name: 'groupList' })
  } else {
    router.push({ name: 'login' })
  }
}
</script>

<style lang="less" scoped>
.site-title {
  font-size: 1.8rem;
  font-weight: bold;
  color: var(--color-text-1);
  line-height: 1.3;
  margin-left: 0.5rem;
}
</style>

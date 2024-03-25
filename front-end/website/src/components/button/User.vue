<template>
  <!-- User Button -->
  <a-dropdown @select="handleSelect" position="br">
    <a-tooltip :content="userStore.nickname" position="br">
      <!-- User Avatar -->
      <a-button :shape="'circle'">
        <template #icon>
          <a-image class="circle" :src="avatar" width="32" height="32" :preview="false" />
        </template>
      </a-button>
    </a-tooltip>
    <!-- Dropdown Options -->
    <template #content>
      <a-doption v-for="item in menuOptions" :key="item" :value="item.key">{{
        $t(item.t)
      }}</a-doption>
    </template>
  </a-dropdown>
  <!-- Your Profile -->
  <Modal v-model:visible="profileVisible" :title="$t('settings.user.profile')">
    <Profile />
  </Modal>
</template>

<script lang="ts">
export enum EUserOption {
  ChangeGroup,
  Profile,
  Logout
}
</script>

<script lang="ts" setup>
import { computed, reactive, ref } from 'vue'

import { useRouter } from 'vue-router'

import useUserStore from '@/stores/user'
import { UserDefaultAvatar } from '@/config/common'

import Modal from '@/layout/Modal.vue'
import Profile from '@/views/profile/index.vue'

const router = useRouter()
const userStore = useUserStore()

const profileVisible = ref(false)

// 菜单选项
const menuOptions = reactive({
  // 切换组织
  [EUserOption.ChangeGroup]: {
    key: EUserOption.ChangeGroup,
    t: 'settings.user.changeGroup',
    handle() {
      router.push({ name: 'groupList' })
    }
  },
  // 打开个人设置
  [EUserOption.Profile]: {
    key: EUserOption.Profile,
    t: 'settings.user.profile',
    handle() {
      profileVisible.value = true
    }
  },
  // 退出登录
  [EUserOption.Logout]: {
    key: EUserOption.Logout,
    t: 'settings.user.logout',
    handle() {
      userStore.logout().finally(() => {
        router.push({ name: 'home' })
      })
    }
  }
})

const avatar = computed(() => userStore.avatar ?? UserDefaultAvatar)

const handleSelect = (value: EUserOption) => {
  menuOptions[value].handle()
}
</script>

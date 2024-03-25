<template>
  <a-space class="other-setting" direction="vertical" :size="12" fill>
    <!-- Language -->
    <a-row class="other-setting-item" align="center" :gutter="12">
      <a-col :span="6" class="other-setting-item-title">
        {{ $t('profile.setting.lang') }}
      </a-col>
      <a-col :span="18">
        <a-select
          class="other-setting-item-select"
          :placeholder="$t('profile.setting.lang.ps')"
          :default-value="currentLocale"
          @change="handleSelectLang"
        >
          <a-option v-for="item in locales" :key="item.value" :value="item.value">{{
            item.label
          }}</a-option>
        </a-select>
      </a-col>
    </a-row>
    <!-- Theme -->
    <a-row class="other-setting-item" align="center" :gutter="12">
      <a-col :span="6" class="other-setting-item-title">
        {{ $t('profile.setting.theme') }}
      </a-col>
      <a-col :span="18">
        <a-select
          class="other-setting-item-select"
          :placeholder="$t('profile.setting.theme.ps')"
          :default-value="currentTheme"
          @change="handleSelectTheme"
        >
          <a-option v-for="item in themes" :key="item.value" :value="item.value">{{
            $t(item.label)
          }}</a-option>
        </a-select>
      </a-col>
    </a-row>
    <!-- Side -->
    <a-row class="other-setting-item" align="center" :gutter="12">
      <a-col :span="6" class="other-setting-item-title">
        {{ $t('profile.setting.side') }}
      </a-col>
      <a-col :span="18">
        <a-select
          class="other-setting-item-select"
          :placeholder="$t('profile.setting.side.ps')"
          :default-value="currentSide"
          @change="handleSelectSide"
        >
          <a-option v-for="item in side" :key="item.value" :value="item.value">{{
            $t(item.label)
          }}</a-option>
        </a-select>
      </a-col>
    </a-row>
    <!-- Frame -->
    <a-row class="other-setting-item" align="center" :gutter="12">
      <a-col :span="6" class="other-setting-item-title">
        {{ $t('profile.setting.frame') }}
      </a-col>
      <a-col :span="18">
        <a-input-number
          v-model="frame"
          class="other-setting-item-select"
          :placeholder="$t('profile.setting.frame.ps')"
          :max="0.7"
          :min="0.3"
        />
      </a-col>
    </a-row>
    <!-- Submit -->
    <a-row class="other-setting-item" align="center" :gutter="12">
      <a-col :span="6" class="other-setting-item-title">
        <!-- only layout -->
      </a-col>
      <a-col :span="18">
        <a-button type="primary" @click="handleSubmit">{{ $t('profile.setting.submit') }}</a-button>
      </a-col>
    </a-row>
  </a-space>
</template>

<script lang="ts" setup>
import { computed, reactive, ref, watch } from 'vue'

import { LOCALE_OPTIONS } from '@/i18n'

import type { IGetProfileResponse } from '../api'

import { useI18n } from 'vue-i18n'

import useLocale from '@/hooks/locale'
import useTheme, { ETheme } from '@/hooks/theme'
import useUserStore from '@/stores/user'

import { Message } from '@arco-design/web-vue'

const props = defineProps<{
  profile?: IGetProfileResponse
}>()
const emit = defineEmits(['onFinally', 'onBeforeSubmit'])

const userStore = useUserStore()
const { currentTheme, changeTheme } = useTheme()

const { t } = useI18n()
const { currentLocale, changeLocale } = useLocale()

// 可选语言列表
const locales = reactive([...LOCALE_OPTIONS])
// 可选主题列表
const themes = reactive([
  {
    label: 'profile.setting.theme.dark',
    value: ETheme.DARK
  },
  {
    label: 'profile.setting.theme.light',
    value: ETheme.LIGHT
  }
])
// 设置主题
const theme = ref<string>()
// 折叠设置列表
const side = reactive([
  {
    label: 'profile.setting.side.indent',
    value: 'Indent'
  },
  {
    label: 'profile.setting.side.outdent',
    value: 'Outdent'
  }
])
// 当前折叠模式
const currentSide = computed(() => userStore.setting?.side)
// 折叠值
const sideValue = ref<string>()
// 当前比例
const frame = ref<number>()

// 监听用户数据是否改变
watch(
  () => props.profile,
  (val) => {
    // 设置主题
    theme.value = val?.setting.theme
    // 设置语言
    if (val?.setting.lang) {
      changeLocale(val?.setting.lang)
    }
    // 比例
    frame.value = Number(val?.setting.frame)
  },
  {
    immediate: true
  }
)

/**
 * 提交修改
 */
const handleSubmit = () => {
  emit('onBeforeSubmit')
  userStore
    .updateUserData({
      lang: currentLocale.value,
      theme: theme.value,
      frame: frame.value,
      side: sideValue.value
    })
    .then(() => {
      // 修改成功返回提示
      Message.success(t('profile.setting.success'))
    })
    .finally(() => {
      emit('onFinally')
    })
}

/**
 * 语言改变
 */
const handleSelectLang = (val: string) => {
  changeLocale(val)
}

/**
 * 主题改变
 */
const handleSelectTheme = (val: string) => {
  theme.value = val
  changeTheme(val as ETheme)
}
/**
 * 折叠值改变
 */
const handleSelectSide = (val: string) => {
  sideValue.value = val
}
</script>

<style lang="less">
.other-setting-item {
  &-title {
    text-align: right;
  }

  &-select {
    display: flex;
    width: 100%;
    max-width: 20rem;
  }
}
</style>

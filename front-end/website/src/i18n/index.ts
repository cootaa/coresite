import { createI18n } from 'vue-i18n'

import enUS from './locale/en-US'
import zhCN from './locale/zh-CN'

import { LS_LANG } from '@/config/localstorageKey'

export const LOCALE_OPTIONS = [
  { label: '简体中文', value: 'zh-CN' },
  { label: 'English', value: 'en-US' }
]

const defaultLocale = localStorage.getItem(LS_LANG) || 'en-US'

const i18n = createI18n({
  locale: defaultLocale,
  fallbackLocale: 'en-US',
  legacy: false,
  allowComposition: true,
  messages: {
    'en-US': enUS,
    'zh-CN': zhCN
  }
})

export default i18n

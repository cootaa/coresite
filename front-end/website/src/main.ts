import { createApp } from 'vue'

import ArcoVueIcon from '@arco-design/web-vue/es/icon'

import pinia from './stores'

import i18n from './i18n'

import App from './App.vue'
import router from './router'

import '@/utils/interceptor'

import '@/assets/style/global.less'

import 'moment/dist/locale/zh-cn'

const app = createApp(App)

app.use(pinia)
app.use(router)
app.use(ArcoVueIcon)
app.use(i18n)

app.mount('#app')

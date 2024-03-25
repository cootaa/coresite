import { ref, watch } from 'vue'
import { LS_THEME } from '@/config/localstorageKey'

export enum ETheme {
  LIGHT = 'light',
  DARK = 'dark'
}

export default function useTheme() {
  const currentTheme = ref<ETheme>((localStorage.getItem(LS_THEME) as ETheme) || ETheme.LIGHT)

  watch(currentTheme, (theme) => {
    if (theme === ETheme.DARK) {
      localStorage.setItem(LS_THEME, ETheme.DARK)
      document.body.setAttribute('arco-theme', 'dark')
    } else {
      localStorage.setItem(LS_THEME, ETheme.LIGHT)
      document.body.removeAttribute('arco-theme')
    }
  })

  const toggleTheme = () => {
    if (currentTheme.value === ETheme.DARK) {
      currentTheme.value = ETheme.LIGHT
    } else {
      currentTheme.value = ETheme.DARK
    }
  }

  const changeTheme = (theme: ETheme) => {
    currentTheme.value = theme
  }

  const initTheme = () => {
    if (currentTheme.value == ETheme.DARK) {
      document.body.setAttribute('arco-theme', 'dark')
    } else {
      document.body.removeAttribute('arco-theme')
    }
  }
  return {
    currentTheme,
    initTheme,
    toggleTheme,
    changeTheme
  }
}

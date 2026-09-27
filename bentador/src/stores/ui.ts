import { defineStore } from 'pinia'
import { computed, ref, watch } from 'vue'

export type ColorTheme = 'green' | 'blue'

const readColorTheme = (): ColorTheme =>
  localStorage.getItem('colorTheme') === 'green' ? 'green' : 'blue'

export const useUiStore = defineStore('ui', () => {
  const isSidebarOpen = ref(false)
  const isDarkMode = ref(localStorage.getItem('darkMode') === 'true')
  const colorTheme = ref<ColorTheme>(readColorTheme())

  const toggleSidebar = () => {
    isSidebarOpen.value = !isSidebarOpen.value
  }

  const setSidebar = (value: boolean) => {
    isSidebarOpen.value = value
  }

  const toggleDarkMode = () => {
    isDarkMode.value = !isDarkMode.value
  }

  const avatarBackground = computed(() => (colorTheme.value === 'blue' ? '0F2573' : '0D9488'))

  const setColorTheme = (theme: ColorTheme) => {
    colorTheme.value = theme
  }

  watch(isDarkMode, (val) => {
    localStorage.setItem('darkMode', val.toString())
    if (val) {
      document.documentElement.classList.add('dark')
    } else {
      document.documentElement.classList.remove('dark')
    }
  }, { immediate: true })

  watch(colorTheme, (theme) => {
    localStorage.setItem('colorTheme', theme)
    document.documentElement.classList.toggle('theme-blue', theme === 'blue')
  }, { immediate: true })

  return {
    isSidebarOpen,
    isDarkMode,
    colorTheme,
    avatarBackground,
    toggleSidebar,
    setSidebar,
    toggleDarkMode,
    setColorTheme,
  }
})

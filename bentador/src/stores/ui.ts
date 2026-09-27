import { defineStore } from 'pinia'
import { ref, watch } from 'vue'

export const useUiStore = defineStore('ui', () => {
  const isSidebarOpen = ref(false)
  const isDarkMode = ref(localStorage.getItem('darkMode') === 'true')

  const toggleSidebar = () => {
    isSidebarOpen.value = !isSidebarOpen.value
  }

  const setSidebar = (value: boolean) => {
    isSidebarOpen.value = value
  }

  const toggleDarkMode = () => {
    isDarkMode.value = !isDarkMode.value
  }

  watch(isDarkMode, (val) => {
    localStorage.setItem('darkMode', val.toString())
    if (val) {
      document.documentElement.classList.add('dark')
    } else {
      document.documentElement.classList.remove('dark')
    }
  }, { immediate: true })

  return {
    isSidebarOpen,
    isDarkMode,
    toggleSidebar,
    setSidebar,
    toggleDarkMode
  }
})

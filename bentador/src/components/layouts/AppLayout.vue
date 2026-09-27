<script setup lang="ts">
import { useUiStore } from '@/stores/ui'
import Sidebar from '@/components/layouts/Sidebar.vue'

const uiStore = useUiStore()
</script>

<template>
  <div
    class="min-h-screen bg-[#F9FAFB] dark:bg-dark-bg text-gray-800 dark:text-slate-200 overflow-x-hidden font-inter transition-colors duration-300"
  >
    <!-- MOBILE SIDEBAR OVERLAY -->
    <Transition
      enter-active-class="transition-opacity duration-300"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="transition-opacity duration-300"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div
        v-if="uiStore.isSidebarOpen"
        @click="uiStore.setSidebar(false)"
        class="fixed inset-0 bg-black/50 z-40 lg:hidden backdrop-blur-sm"
      ></div>
    </Transition>

    <Sidebar />

    <!-- MAIN CONTENT -->
    <main class="lg:ml-64 min-h-screen transition-all duration-300">
      <router-view />
    </main>
  </div>
</template>

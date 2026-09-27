<script lang="ts">
export default {
  name: 'ProductHeader',
}
</script>

<script setup lang="ts">
import { ref } from 'vue'
import { useUiStore } from '@/stores/ui'
import { useProductStore } from '@/stores/products'
import { Bars3Icon, HomeIcon, PlusIcon, ChevronDownIcon } from '@heroicons/vue/24/outline'

const uiStore = useUiStore()
const productStore = useProductStore()

const actionsOpen = ref(false)
</script>

<template>
  <header class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
    <!-- Header Info -->
    <div class="flex items-center gap-4">
      <button
        @click="uiStore.setSidebar(true)"
        class="lg:hidden p-2 bg-white dark:bg-dark-card border border-gray-200 dark:border-dark-border rounded-xl shadow-sm hover:bg-gray-50 dark:hover:bg-slate-800 transition"
        type="button"
      >
        <Bars3Icon class="w-6 h-6" />
      </button>
      <div>
        <nav class="flex items-center gap-2 text-[12px] text-gray-400 mb-1">
          <HomeIcon class="w-3 h-3" />
          <span>/</span>
          <span class="text-teal-600 dark:text-teal-400 font-medium">Products</span>
        </nav>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Products List</h1>
        <p class="text-gray-500 dark:text-slate-400 text-sm">
          Manage your inventory of
          <span class="text-teal-600 dark:text-teal-400 font-bold">2,401,120</span> items
        </p>
      </div>
    </div>

    <!-- Header Actions -->
    <div class="flex gap-3 w-full md:w-auto">
      <button
        @click="productStore.toggleModal('add', true)"
        class="flex-1 md:flex-none bg-teal-500 hover:bg-teal-600 text-white px-5 py-2.5 rounded-xl text-sm font-bold flex items-center justify-center gap-2 shadow-lg shadow-teal-500/20 transition-all active:scale-95"
        type="button"
      >
        <PlusIcon class="w-4 h-4" /> Add Product
      </button>
      <div class="relative">
        <button
          @click.stop="actionsOpen = !actionsOpen"
          class="bg-white dark:bg-dark-card border border-gray-200 dark:border-dark-border px-4 py-2.5 rounded-xl text-sm font-bold text-gray-700 dark:text-slate-300 flex items-center gap-2 hover:bg-gray-50 dark:hover:bg-slate-800 transition"
          type="button"
        >
          Actions <ChevronDownIcon class="w-4 h-4 text-gray-400" />
        </button>
        <Transition
          enter-active-class="transition ease-out duration-100"
          enter-from-class="opacity-0 scale-95"
          enter-to-class="opacity-100 scale-100"
        >
          <div
            v-if="actionsOpen"
            v-outside-click="() => (actionsOpen = false)"
            class="absolute right-0 mt-2 w-48 bg-white dark:bg-dark-card border border-gray-200 dark:border-dark-border shadow-xl rounded-xl overflow-hidden z-50"
          >
            <button
              @click="actionsOpen = false"
              class="w-full text-left px-4 py-2.5 text-sm text-gray-600 dark:text-slate-400 hover:bg-gray-50 dark:hover:bg-slate-800 transition"
              type="button"
            >
              Export CSV
            </button>
            <button
              @click="actionsOpen = false"
              class="w-full text-left px-4 py-2.5 text-sm text-gray-600 dark:text-slate-400 hover:bg-gray-50 dark:hover:bg-slate-800 transition"
              type="button"
            >
              Import Bulk
            </button>
          </div>
        </Transition>
      </div>
    </div>
  </header>
</template>

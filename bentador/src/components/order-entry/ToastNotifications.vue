<script setup lang="ts">
import { useToastStore } from '@/stores/toast'
import { CheckCircleIcon } from '@heroicons/vue/24/outline'

const toastStore = useToastStore()
</script>

<template>
  <div class="fixed bottom-8 right-8 z-[200] space-y-4 pointer-events-none">
    <TransitionGroup
      enter-active-class="transition ease-out duration-300"
      enter-from-class="translate-y-10 opacity-0"
      enter-to-class="translate-y-0 opacity-100"
      leave-active-class="transition ease-in duration-200"
      leave-from-class="translate-y-0 opacity-100"
      leave-to-class="translate-x-full opacity-0"
    >
      <div
        v-for="toast in toastStore.toasts"
        :key="toast.id"
        class="bg-white dark:bg-dark-card border border-gray-200 dark:border-dark-border px-6 py-4 rounded-2xl shadow-2xl pointer-events-auto flex items-center gap-4 min-w-[300px]"
      >
        <div class="w-10 h-10 bg-teal-500/10 text-teal-600 rounded-xl flex items-center justify-center">
          <CheckCircleIcon class="w-6 h-6" />
        </div>
        <div class="flex-1">
          <p class="text-sm font-black text-gray-900 dark:text-white">Success!</p>
          <p class="text-xs text-gray-500 dark:text-slate-400">
            {{ toast.message }}
          </p>
        </div>
      </div>
    </TransitionGroup>
  </div>
</template>

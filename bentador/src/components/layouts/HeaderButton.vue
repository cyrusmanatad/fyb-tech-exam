<script setup lang="ts">
import { PlusIcon } from '@heroicons/vue/24/outline'

interface Props {
  label: string
  icon?: typeof PlusIcon | null
  variant?: 'primary' | 'secondary'
  disabled?: boolean
}

withDefaults(defineProps<Props>(), {
  icon: PlusIcon,
  variant: 'primary',
  disabled: false,
})

const emit = defineEmits<{
  (e: 'click'): void
}>()
</script>

<template>
  <button
    type="button"
    :disabled="disabled"
    class="flex-1 md:flex-none flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl text-sm font-bold transition-all active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed"
    :class="{
      'bg-teal-500 hover:bg-teal-600 text-white shadow-lg shadow-teal-500/20':
        variant === 'primary',
      'bg-white dark:bg-dark-card border border-gray-200 dark:border-dark-border text-gray-700 dark:text-slate-300 hover:bg-gray-50 dark:hover:bg-slate-800':
        variant === 'secondary',
    }"
    @click="emit('click')"
  >
    <component :is="icon" v-if="icon" class="w-4 h-4 shrink-0" />
    {{ label }}
  </button>
</template>

<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue'
import { ChevronDownIcon } from '@heroicons/vue/24/outline'
import type { DropdownItem } from '@/types/header-types'

interface Props {
  /** Trigger button label */
  label: string

  /** Menu items to render */
  items: DropdownItem[]

  /** Dropdown alignment relative to trigger */
  align?: 'left' | 'right'
}

withDefaults(defineProps<Props>(), {
  align: 'right',
})

// Emits
const emit = defineEmits<{
  (e: 'select', item: DropdownItem): void
}>()

// State
const open = ref<boolean>(false)
const wrapperRef = ref<HTMLElement | null>(null)

// Click-outside handler (no external directive needed)
const onOutsideClick = (e: MouseEvent): void => {
  if (wrapperRef.value && !wrapperRef.value.contains(e.target as Node)) {
    open.value = false
  }
}

onMounted(() => document.addEventListener('mousedown', onOutsideClick))
onUnmounted(() => document.removeEventListener('mousedown', onOutsideClick))

// Keyboard navigation (close on Escape)
const onKeydown = (e: KeyboardEvent): void => {
  if (e.key === 'Escape') open.value = false
}

// Handlers
const selectItem = (item: DropdownItem): void => {
  if (item.disabled) return
  emit('select', item)
  open.value = false
}
</script>

<template>
  <div ref="wrapperRef" class="relative" @keydown="onKeydown">
    <!-- Trigger button -->
    <button
      type="button"
      :aria-expanded="open"
      aria-haspopup="true"
      class="bg-white dark:bg-dark-card border border-gray-200 dark:border-dark-border px-4 py-2.5 rounded-xl text-sm font-bold text-gray-700 dark:text-slate-300 flex items-center gap-2 hover:bg-gray-50 dark:hover:bg-slate-800 transition whitespace-nowrap"
      @click.stop="open = !open"
    >
      {{ label }}
      <ChevronDownIcon
        class="w-4 h-4 text-gray-400 transition-transform duration-200"
        :class="{ 'rotate-180': open }"
      />
    </button>

    <!-- Dropdown panel -->
    <Transition
      enter-active-class="transition ease-out duration-100"
      enter-from-class="opacity-0 scale-95"
      enter-to-class="opacity-100 scale-100"
      leave-active-class="transition ease-in duration-75"
      leave-from-class="opacity-100 scale-100"
      leave-to-class="opacity-0 scale-95"
    >
      <div
        v-if="open"
        role="menu"
        class="absolute mt-2 w-48 bg-white dark:bg-dark-card border border-gray-200 dark:border-dark-border shadow-xl rounded-xl overflow-hidden z-50"
        :class="align === 'right' ? 'right-0' : 'left-0'"
      >
        <!-- Divider group support -->
        <template v-for="(item, i) in items" :key="i">
          <div v-if="item.divider" class="border-t border-gray-100 dark:border-dark-border my-1" />
          <button
            v-else
            type="button"
            role="menuitem"
            :disabled="item.disabled"
            class="w-full text-left px-4 py-2.5 text-sm flex items-center gap-2 transition disabled:opacity-40 disabled:cursor-not-allowed"
            :class="
              item.danger
                ? 'text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-500/10'
                : 'text-gray-600 dark:text-slate-400 hover:bg-gray-50 dark:hover:bg-slate-800'
            "
            @click="selectItem(item)"
          >
            <component :is="item.icon" v-if="item.icon" class="w-4 h-4 shrink-0" />
            {{ item.label }}
          </button>
        </template>
      </div>
    </Transition>
  </div>
</template>

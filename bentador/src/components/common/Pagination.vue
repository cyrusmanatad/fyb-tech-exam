<!-- components/AppPagination.vue -->
<script setup lang="ts">
import { computed } from 'vue'
import { ArrowLeftIcon, ArrowRightIcon } from '@heroicons/vue/24/outline'

interface PaginationMeta {
  current_page: number
  last_page: number
  per_page: number
  total: number
  from: number
  to: number
}

const props = defineProps<{
  meta: PaginationMeta
}>()

const emit = defineEmits<{
  (e: 'page-change', page: number): void
}>()

const pages = computed(() => {
  const current = props.meta.current_page
  const last = props.meta.last_page
  const delta = 1 // pages to show around current
  const range: (number | '...')[] = []

  const left = Math.max(2, current - delta)
  const right = Math.min(last - 1, current + delta)

  // Always show first page
  range.push(1)

  if (left > 2) range.push('...')

  for (let i = left; i <= right; i++) {
    range.push(i)
  }

  if (right < last - 1) range.push('...')

  // Always show last page
  if (last > 1) range.push(last)

  return range
})

const goTo = (page: number | '...') => {
  if (page === '...' || page === props.meta.current_page) return
  emit('page-change', page)
}
</script>

<template>
  <div
    class="p-6 bg-gray-50/30 dark:bg-slate-800/20 border-t border-gray-100 dark:border-dark-border flex flex-col sm:flex-row items-center justify-between gap-4"
  >
    <!-- Previous -->
    <button
      class="w-full sm:w-auto px-4 py-2 border border-gray-200 dark:border-dark-border rounded-xl text-xs font-bold text-gray-600 dark:text-slate-400 hover:bg-white dark:hover:bg-slate-800 transition flex items-center justify-center gap-2 disabled:opacity-40 disabled:cursor-not-allowed"
      type="button"
      :disabled="meta.current_page === 1"
      @click="goTo(meta.current_page - 1)"
    >
      <ArrowLeftIcon class="w-3 h-3" /> Previous
    </button>

    <!-- Page Numbers -->
    <div class="flex items-center gap-2">
      <template v-for="page in pages" :key="page">
        <!-- Ellipsis -->
        <span
          v-if="page === '...'"
          class="w-8 h-8 flex items-center justify-center text-gray-400 dark:text-slate-600 text-xs"
        >
          ...
        </span>

        <!-- Page Button -->
        <button
          v-else
          type="button"
          class="w-8 h-8 rounded-lg font-bold text-xs transition"
          :class="
            page === meta.current_page
              ? 'bg-teal-500 text-white'
              : 'hover:bg-white dark:hover:bg-slate-800 border border-transparent hover:border-gray-200 dark:hover:border-dark-border text-gray-600 dark:text-slate-400'
          "
          @click="goTo(page)"
        >
          {{ page }}
        </button>
      </template>
    </div>

    <!-- Next -->
    <button
      class="w-full sm:w-auto px-4 py-2 border border-gray-200 dark:border-dark-border rounded-xl text-xs font-bold text-gray-600 dark:text-slate-400 hover:bg-white dark:hover:bg-slate-800 transition flex items-center justify-center gap-2 disabled:opacity-40 disabled:cursor-not-allowed"
      type="button"
      :disabled="meta.current_page === meta.last_page"
      @click="goTo(meta.current_page + 1)"
    >
      Next <ArrowRightIcon class="w-3 h-3" />
    </button>
  </div>
</template>

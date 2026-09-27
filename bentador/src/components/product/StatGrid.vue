<script setup lang="ts">
import type { Stat } from '@/types/data-types'

// Props
const props = defineProps<{
  stats: Stat[]
}>()
</script>

<template>
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 mb-8">
    <div
      v-for="stat in props.stats"
      :key="stat.label"
      class="bg-white dark:bg-dark-card p-6 rounded-2xl border border-gray-100 dark:border-dark-border shadow-sm hover:shadow-md transition-shadow group"
    >
      <p
        class="text-sm text-gray-500 dark:text-slate-400 font-medium mb-1"
        :class="stat.isAlert && 'text-red-500 dark:text-red-400'"
      >
        {{ stat.label }}
      </p>
      <div class="flex items-end justify-between">
        <h3
          class="text-2xl font-black text-gray-900 dark:text-white"
          :class="stat.isAlert && 'text-red-500 dark:text-red-400'"
        >
          {{ stat.val }}
        </h3>
        <div
          class="h-1.5 w-10 rounded-full mb-2 transition-all group-hover:w-14"
          :class="stat.color"
        ></div>
      </div>
      <p class="text-[11px] text-gray-400 mt-2">{{ stat.desc }}</p>
    </div>
  </div>
</template>

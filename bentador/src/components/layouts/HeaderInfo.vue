<script setup lang="ts">
import { Bars3Icon, HomeIcon } from '@heroicons/vue/24/outline'
import { useUiStore } from '@/stores/ui'
import type { BreadcrumbItem } from '@/types/header-types'

// Props
interface Props {
  title: string
  description?: string
  breadcrumb?: BreadcrumbItem[]
  showMenuButton?: boolean
}

withDefaults(defineProps<Props>(), {
  description: undefined,
  breadcrumb: () => [],
  showMenuButton: true,
})

const uiStore = useUiStore()
</script>

<template>
  <div class="flex items-center gap-4">
    <!-- Mobile hamburger -->
    <button
      v-if="showMenuButton"
      type="button"
      aria-label="Open sidebar"
      class="lg:hidden p-2 bg-white dark:bg-dark-card border border-gray-200 dark:border-dark-border rounded-xl shadow-sm hover:bg-gray-50 dark:hover:bg-slate-800 transition"
      @click="uiStore.setSidebar(true)"
    >
      <Bars3Icon class="w-6 h-6" />
    </button>

    <!-- Title block -->
    <div>
      <!-- Breadcrumb -->
      <nav class="flex items-center gap-1.5 text-[12px] text-gray-400 mb-1" aria-label="Breadcrumb">
        <HomeIcon class="w-3 h-3 shrink-0" />

        <template v-for="(crumb, i) in breadcrumb" :key="i">
          <span class="select-none">/</span>
          <component
            :is="crumb.to ? 'RouterLink' : 'span'"
            :to="crumb.to"
            class="text-teal-600 dark:text-teal-400 font-medium"
            :class="crumb.to ? 'hover:underline' : 'cursor-default'"
          >
            {{ crumb.label }}
          </component>
        </template>
      </nav>

      <!-- Heading -->
      <h1 class="text-2xl font-bold text-gray-900 dark:text-white leading-tight">
        {{ title }}
      </h1>

      <!-- Description: prop or rich slot -->
      <p
        v-if="$slots.description || description"
        class="text-gray-500 dark:text-slate-400 text-sm mt-0.5"
      >
        <slot name="description">{{ description }}</slot>
      </p>
    </div>
  </div>
</template>

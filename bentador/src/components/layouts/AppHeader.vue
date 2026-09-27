<script setup lang="ts">
import HeaderInfo from './HeaderInfo.vue'
import HeaderActions from './HeaderActions.vue'
import type {
  BreadcrumbItem,
  ActionItem,
  ButtonAction,
  DropdownItem,
  DropdownAction,
} from '@/types/header-types'

// Props
interface Props {
  title: string
  description?: string
  breadcrumb?: BreadcrumbItem[]
  showMenuButton?: boolean
  /**
   * All right-side actions in one array.
   * Each item is either a ButtonAction or a DropdownAction —
   * HeaderActions renders the correct element based on `type`.
   *
   * @example
   * :action-items="[
   *   { key: 'add',     type: 'button',   label: 'Add Product', handler: openAdd },
   *   { key: 'actions', type: 'dropdown', label: 'Actions',     items: [...] },
   * ]"
   */
  actionItems?: ActionItem[]
}

withDefaults(defineProps<Props>(), {
  description: undefined,
  breadcrumb: () => [],
  showMenuButton: true,
  actionItems: () => [],
})

// Emits — bubble up from HeaderActions for pages that prefer
// handling events at the top level instead of via handlers
const emit = defineEmits<{
  (e: 'action', item: ButtonAction): void
  (e: 'select', item: DropdownItem, parent: DropdownAction): void
}>()
</script>

<template>
  <header class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
    <!-- Left: Info -->
    <HeaderInfo
      :title="title"
      :description="description"
      :breadcrumb="breadcrumb"
      :show-menu-button="showMenuButton"
    >
      <template v-if="$slots.description" #description>
        <slot name="description" />
      </template>
    </HeaderInfo>

    <!-- Right: Actions -->
    <HeaderActions
      v-if="actionItems.length"
      :action-items="actionItems"
      @action="(item) => emit('action', item)"
      @select="(item, parent) => emit('select', item, parent)"
    />
  </header>
</template>

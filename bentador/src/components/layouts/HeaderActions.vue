<script setup lang="ts">
/**
 * HeaderActions
 *
 * Accepts a single :action-items array of ActionItem objects.
 * Each item carries its own `type` ('button' | 'dropdown') so
 * this component renders the right element automatically.
 *
 * No need to manually compose <HeaderButton> and <AppDropdown>
 * at the page level — just describe what you want in the array.
 */

// import HeaderButton from '@/components/layout/HeaderButton.vue'
import AppDropdown from '@/components/ui/AppDropdown.vue'
import type { ActionItem, ButtonAction, DropdownAction, DropdownItem } from '@/types/header-types'
import HeaderButton from './HeaderButton.vue'

defineProps<{
  actionItems: ActionItem[]
}>()

const emit = defineEmits<{
  /** Bubbles up every button click with its ActionItem */
  (e: 'action', item: ButtonAction): void
  /** Bubbles up every dropdown row selection */
  (e: 'select', item: DropdownItem, parent: DropdownAction): void
}>()

// Type guards
const isButton = (item: ActionItem): item is ButtonAction => item.type === 'button'
const isDropdown = (item: ActionItem): item is DropdownAction => item.type === 'dropdown'

// Handlers
const handleButton = (item: ButtonAction): void => {
  item.handler()
  emit('action', item)
}

const handleSelect = (dropdownItem: DropdownItem, parent: DropdownAction): void => {
  dropdownItem.handler?.()
  parent.onSelect?.(dropdownItem)
  emit('select', dropdownItem, parent)
}
</script>

<template>
  <div class="flex items-center gap-3 w-full md:w-auto">
    <template v-for="item in actionItems" :key="item.key">
      <!-- Button -->
      <HeaderButton
        v-if="isButton(item)"
        :label="item.label"
        :icon="item.icon"
        :variant="item.variant"
        :disabled="item.disabled"
        @click="handleButton(item)"
      />

      <!-- Dropdown -->
      <AppDropdown
        v-else-if="isDropdown(item)"
        :label="item.label"
        :icon="item.icon"
        :items="item.items"
        :align="item.align"
        @select="(row) => handleSelect(row, item)"
      />
    </template>
  </div>
</template>

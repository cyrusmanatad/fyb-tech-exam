<script setup lang="ts">
import StatGrid from '@/components/product/StatGrid.vue'
import ProductTable from '@/components/product/ProductTable.vue'
import ProductModal from '@/components/product/modals/ProductModal.vue'
import DeleteModal from '@/components/product/modals/DeleteModal.vue'
import LogoutModal from '@/components/product/modals/LogoutModal.vue'
import AppHeader from '@/components/layouts/AppHeader.vue'
import type { ActionItem, DropdownAction, DropdownItem } from '@/types/header-types'
import { useProductStore } from '@/stores/products'
import {
  ArrowDownTrayIcon,
  ArrowUpTrayIcon,
  ListBulletIcon,
  PlusIcon,
  TrashIcon,
} from '@heroicons/vue/24/outline'
import { onMounted } from 'vue'
import { useCategoryStore } from '@/stores/category'
import { useAuthStore } from '@/stores/auth'

const productStore = useProductStore()
const authStore = useAuthStore()
const categoryStore = useCategoryStore()

const actionItems: ActionItem[] = []

const dropdownAction: DropdownAction = {
  key: 'actions',
  type: 'dropdown',
  label: 'Actions',
  items: [
    {
      label: 'Categories',
      icon: ListBulletIcon,
      handler: () => productStore.exportCsv(),
    },
    {
      label: 'Export CSV',
      icon: ArrowDownTrayIcon,
      handler: () => productStore.exportCsv(),
    },
    {
      label: 'Import Bulk',
      icon: ArrowUpTrayIcon,
      handler: () => productStore.importBulk(),
    },
  ],
}

if (authStore.hasPermission(['create products'])) {
  actionItems.push({
    key: 'add',
    type: 'button',
    label: 'Add Product',
    icon: PlusIcon,
    variant: 'primary',
    handler: () => productStore.toggleModal('add', true),
  })
}

if (authStore.hasPermission(['delete all products'])) {
  dropdownAction.items.push({ label: '', divider: true })
  dropdownAction.items.push({
    label: 'Delete All',
    icon: TrashIcon,
    danger: true,
    handler: () => productStore.deleteAll(),
  })
}

actionItems.push(dropdownAction)

onMounted(async () => {
  await Promise.all([productStore.fetchStatistics(), categoryStore.fetchCategories()])
})
</script>

<template>
  <div class="p-4 md:p-8">
    <!-- <Header /> -->
    <AppHeader
      title="Products List"
      :breadcrumb="[{ label: 'Products' }]"
      :action-items="actionItems"
    >
      <template #description>
        Manage your inventory of
        <span class="text-teal-600 dark:text-teal-400 font-bold">2,401,120</span> items
      </template>
    </AppHeader>

    <StatGrid :stats="productStore.stats" />
    <ProductTable />

    <!-- Modals -->
    <ProductModal mode="add" />
    <ProductModal mode="edit" />
    <DeleteModal />
  </div>
</template>

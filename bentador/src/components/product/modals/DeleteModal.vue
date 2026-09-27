<script setup lang="ts">
import { useProductStore } from '@/stores/products'
import BaseModal from '@/components/common/BaseModal.vue'
import { ExclamationTriangleIcon } from '@heroicons/vue/24/outline'

const productStore = useProductStore()

const confirmDelete = () => {
  if (productStore.selectedProduct) {
    productStore.deleteProduct(productStore.selectedProduct.id)
  }
}
</script>

<template>
  <BaseModal 
    :show="productStore.modals.delete" 
    @close="productStore.toggleModal('delete', false)"
    maxWidth="max-w-sm"
  >
    <div class="p-8 text-center">
      <div class="w-20 h-20 bg-red-100 dark:bg-red-500/10 text-red-600 dark:text-red-400 rounded-full flex items-center justify-center mx-auto mb-6 shadow-inner">
        <ExclamationTriangleIcon class="w-10 h-10" />
      </div>
      <h2 class="text-xl font-black text-gray-900 dark:text-white mb-2">Delete Item?</h2>
      <p class="text-sm text-gray-500 dark:text-slate-400 mb-8 leading-relaxed">
        Are you sure you want to remove <span class="font-bold text-gray-900 dark:text-white">{{ productStore.selectedProduct?.title }}</span>? This cannot be undone.
      </p>
      <div class="flex flex-col gap-3">
        <button 
          @click="confirmDelete" 
          class="w-full py-3.5 bg-red-500 hover:bg-red-600 text-white font-black rounded-2xl shadow-xl shadow-red-500/30 transition active:scale-95"
          type="button"
        >
          Yes, Delete Permanent
        </button>
        <button 
          @click="productStore.toggleModal('delete', false)" 
          class="w-full py-3 text-gray-400 dark:text-slate-500 font-bold hover:text-gray-900 dark:hover:text-white transition"
          type="button"
        >
          Cancel Action
        </button>
      </div>
    </div>
  </BaseModal>
</template>

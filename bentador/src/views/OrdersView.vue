<script setup lang="ts">
import { ref, watch } from 'vue'
import {
  ArrowDownTrayIcon,
  XMarkIcon,
  ExclamationTriangleIcon,
} from '@heroicons/vue/24/outline'
import BaseModal from '@/components/common/BaseModal.vue'
import AppHeader from '@/components/layouts/AppHeader.vue'
import type { ActionItem } from '@/types/header-types'
import StatGrid from '@/components/product/StatGrid.vue'
import { useOrderStore } from '@/stores/transactions'
import TransactionsTable from '@/components/modules/sales/TransactionsTable.vue'
import { ORDER_STATUS } from '@/types/enum'
import { useDownload } from '@/composables/useDownload'

const orderStore = useOrderStore()
const { downloading, downloadPdf } = useDownload()

const orderStatus = ref('')

const actionItems: ActionItem[] = [
  {
    key: 'export-report',
    type: 'button',
    label: !downloading ? 'Generating...' : 'Download Report',
    icon: ArrowDownTrayIcon,
    variant: 'primary',
    handler: async () => downloadPdf('api/v1/orders/export'),
  },
]

watch(
  () => orderStore.selectedOrder,
  () => {
    orderStatus.value = orderStore.selectedOrder?.status || ''
  },
)
</script>

<template>
  <div class="p-4 md:p-8">
    <!-- HEADER -->
    <AppHeader title="Orders List" :breadcrumb="[{ label: 'Orders' }]" :action-items="actionItems">
      <template #description>
        You have
        <span class="text-teal-600 dark:text-teal-400 font-bold">{{ orderStore.ordersToday }}</span>
        new order/s today
      </template>
    </AppHeader>

    <!-- STATS GRID -->
    <StatGrid :stats="orderStore.stats" />

    <!-- TABLE SECTION -->
    <TransactionsTable />

    <!-- Modals -->
    <BaseModal :show="orderStore.modals.details" @close="orderStore.modals.details = false">
      <div
        class="p-6 border-b dark:border-dark-border flex justify-between items-center bg-gray-50/50 dark:bg-slate-800/20"
      >
        <div>
          <h2 class="text-xl font-black text-gray-900 dark:text-white">Order Details</h2>
          <p class="text-xs text-teal-600 dark:text-teal-400 font-bold uppercase tracking-wider">
            {{ orderStore.selectedOrder?.order_number }}
          </p>
        </div>
        <button
          @click="orderStore.modals.details = false"
          class="p-2 hover:bg-gray-200 dark:hover:bg-slate-800 rounded-full transition text-gray-400 dark:text-slate-500"
          type="button"
        >
          <XMarkIcon class="w-5 h-5" />
        </button>
      </div>
      <div class="p-6 space-y-6 overflow-y-auto custom-scrollbar">
        <div class="grid grid-cols-2 gap-6">
          <div>
            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1"
              >Customer</label
            >
            <p class="text-sm font-bold text-gray-900 dark:text-white">
              {{ orderStore.selectedOrder?.customer }}
            </p>
          </div>
          <div>
            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1"
              >Status</label
            >
            <select
              v-model="orderStatus"
              class="w-full px-3 py-2 bg-gray-50 dark:text-slate-400 dark:bg-slate-900 border border-gray-200 dark:border-dark-border rounded-xl text-xs font-bold focus:ring-2 focus:ring-teal-500/20 outline-none"
            >
              <option v-for="status in ORDER_STATUS" :key="status.code" :value="status.code">
                {{ status.label }}
              </option>
            </select>
          </div>
        </div>
        <div class="border-t dark:border-dark-border pt-4">
          <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3"
            >Order Items</label
          >
          <div class="space-y-3">
            <div
              v-for="item in orderStore.selectedOrder?.items"
              :key="item.id"
              class="flex justify-between items-center text-sm"
            >
              <span class="text-gray-600 dark:text-slate-400">
                {{ item.quantity }}x {{ item.product_name }}</span
              >
              <span class="font-bold dark:text-slate-400">{{ item.subtotal }}</span>
            </div>
          </div>
        </div>
        <div class="border-t dark:border-dark-border pt-4">
          <div class="space-y-3">
            <div class="flex justify-between items-center text-sm">
              <span class="text-gray-600 dark:text-slate-400"> Total</span>
              <span class="font-bold dark:text-slate-400">{{
                orderStore.selectedOrder?.total
              }}</span>
            </div>
          </div>
        </div>
      </div>
      <div
        class="p-6 bg-gray-50 dark:bg-slate-800/20 border-t dark:border-dark-border flex justify-end gap-3"
      >
        <button
          @click="orderStore.updateOrderStatus(orderStatus)"
          class="px-6 py-2.5 text-sm font-bold text-white bg-teal-500 hover:bg-teal-600 rounded-xl shadow-lg transition"
          type="button"
        >
          Save Updates
        </button>
      </div>
    </BaseModal>

    <BaseModal
      :show="orderStore.modals.delete"
      @close="orderStore.modals.delete = false"
      maxWidth="max-w-sm"
    >
      <div class="p-8 text-center">
        <div
          class="w-20 h-20 bg-red-100 dark:bg-red-500/10 text-red-600 dark:text-red-400 rounded-full flex items-center justify-center mx-auto mb-6"
        >
          <ExclamationTriangleIcon class="w-10 h-10" />
        </div>
        <h2 class="text-xl font-black text-gray-900 dark:text-white mb-2">Cancel Order?</h2>
        <p class="text-sm text-gray-500 dark:text-slate-400 mb-8 leading-relaxed">
          Are you sure you want to cancel
          <span class="font-bold text-gray-900 dark:text-white">{{
            orderStore.selectedOrder?.order_number
          }}</span
          >? This will notify the customer.
        </p>
        <div class="flex flex-col gap-3">
          <button
            @click="orderStore.deleteOrder"
            class="w-full py-3.5 bg-red-500 hover:bg-red-600 text-white font-black rounded-2xl shadow-xl transition"
            type="button"
          >
            Yes, Cancel Order
          </button>
          <button
            @click="orderStore.modals.delete = false"
            class="w-full py-3 text-gray-400 font-bold hover:text-gray-900 dark:hover:text-white transition"
            type="button"
          >
            Keep Order
          </button>
        </div>
      </div>
    </BaseModal>
  </div>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
  width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background: #e2e8f0;
  border-radius: 10px;
}
.dark .custom-scrollbar::-webkit-scrollbar-thumb {
  background: #334155;
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(8px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-in {
  animation: fadeIn 0.3s ease-out forwards;
}
</style>

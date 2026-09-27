<script setup lang="ts">
import { computed, onMounted, ref, watch } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { useOrderStore } from '@/stores/transactions'
import {
  MagnifyingGlassIcon,
  ChevronDownIcon,
  CalendarIcon,
  EyeIcon,
  TrashIcon,
} from '@heroicons/vue/24/outline'
import TableSpinner from '@/components/ui/TableSpinner.vue'
import Pagination from '@/components/common/Pagination.vue'
import { useDebounceFn } from '@vueuse/core'
import { storeToRefs } from 'pinia'
import type { Status } from '@/types/data-types'
import { OrderStatus, OrderStatusLabel } from '@/types/enum'

const transaction = useOrderStore()

const { search } = storeToRefs(transaction)

const selectedStatus = ref<Status<OrderStatus, OrderStatusLabel> | null>({
  code: OrderStatus.ALL,
  label: OrderStatusLabel.ALL,
})

const activeFilter = ref<'status' | 'dateRange' | null>(null)

const statusOpen = computed(() => activeFilter.value === 'status')
const dateRangeOpen = computed(() => activeFilter.value === 'dateRange')

const dateFrom = ref('')
const dateTo = ref('')

const handleStatus = async (status: Status<OrderStatus, OrderStatusLabel>) => {
  selectedStatus.value = status
  activeFilter.value = null
  await transaction.fetchOrders(search.value, 1, {
    status: status.code,
  })
}

const handleClearDateRange = () => {
  dateFrom.value = ''
  dateTo.value = ''
}

const onPageChange = (page: number) => {
  transaction.fetchOrders(search.value, page)
}

const debouncedFetch = useDebounceFn(() => {
  transaction.fetchOrders(search.value, 1, {
    status: selectedStatus.value?.code,
  })
}, 400)

watch(search, () => {
  debouncedFetch()
})

onMounted(async () => {
  await Promise.all([transaction.fetchOrders(), transaction.fetchStatistics()])
})
</script>

<template>
  <div
    class="bg-white dark:bg-dark-card rounded-2xl border border-gray-200 dark:border-dark-border shadow-sm overflow-hidden"
  >
    <!-- Filter Bar -->
    <div
      class="p-4 border-b border-gray-100 dark:border-dark-border flex flex-col lg:flex-row gap-4 justify-between bg-gray-50/30 dark:bg-slate-800/20"
    >
      <div class="relative max-w-md w-full">
        <MagnifyingGlassIcon class="absolute left-3 top-2.5 w-4 h-4 text-gray-400" />
        <input
          type="text"
          v-model="transaction.search"
          placeholder="Search Order ID, Customer..."
          class="w-full pl-10 pr-4 py-2 bg-white dark:bg-slate-900 border border-gray-200 dark:border-dark-border rounded-xl text-sm dark:text-white focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 outline-none transition"
        />
      </div>
      <div class="flex flex-wrap gap-2 pb-1 lg:pb-0">
        <!-- Status Dropdown -->
        <div class="relative">
          <button
            @click.stop="activeFilter = 'status'"
            type="button"
            class="whitespace-nowrap px-4 py-2 border border-gray-200 dark:border-dark-border rounded-xl text-xs font-bold bg-white dark:bg-slate-900 dark:text-slate-300 hover:bg-gray-50 dark:hover:bg-slate-800 transition flex items-center gap-2"
          >
            <span>{{ selectedStatus?.label }}</span>
            <ChevronDownIcon
              class="w-3 h-3 text-gray-400 transition-transform"
              :class="statusOpen && 'rotate-180'"
            />
          </button>
          <Transition
            enter-active-class="transition ease-out duration-100"
            enter-from-class="opacity-0 scale-95"
            enter-to-class="opacity-100 scale-100"
          >
            <div
              v-if="statusOpen"
              v-outside-click="() => (activeFilter = null)"
              class="absolute left-0 mt-2 w-44 bg-white dark:bg-dark-card border border-gray-200 dark:border-dark-border shadow-xl rounded-xl z-[70] py-1"
            >
              <button
                v-for="status in transaction.statuses"
                :key="status.code"
                @click.stop="handleStatus(status)"
                class="w-full text-left px-4 py-2 text-sm text-gray-600 dark:text-slate-400 hover:bg-teal-50 dark:hover:bg-teal-500/10 hover:text-teal-600 dark:hover:text-teal-400 transition"
                type="button"
              >
                {{ status.label }}
              </button>
            </div>
          </Transition>
        </div>

        <!-- Date Range Dropdown -->
        <div class="relative">
          <button
            @click.stop="activeFilter = 'dateRange'"
            type="button"
            class="whitespace-nowrap px-4 py-2 border border-gray-200 dark:border-dark-border rounded-xl text-xs font-bold bg-white dark:bg-slate-900 dark:text-slate-300 hover:bg-gray-50 dark:hover:bg-slate-800 transition flex items-center gap-2"
          >
            <CalendarIcon class="w-3 h-3 text-teal-500 dark:text-teal-400" />
            <span>{{ dateFrom && dateTo ? dateFrom + ' - ' + dateTo : 'Select Date Range' }}</span>
            <ChevronDownIcon
              class="w-3 h-3 text-gray-400 transition-transform"
              :class="dateRangeOpen && 'rotate-180'"
            />
          </button>
          <Transition
            enter-active-class="transition ease-out duration-100"
            enter-from-class="opacity-0 scale-95"
            enter-to-class="opacity-100 scale-100"
          >
            <div
              v-if="dateRangeOpen"
              v-outside-click="() => (activeFilter = null)"
              class="absolute right-0 mt-2 p-4 bg-white dark:bg-dark-card border border-gray-200 dark:border-dark-border shadow-2xl rounded-2xl z-[70] min-w-[280px]"
            >
              <div class="grid grid-cols-2 gap-3">
                <div>
                  <label
                    class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1"
                    >From</label
                  >
                  <input
                    type="date"
                    v-model="dateFrom"
                    class="w-full px-2 py-2 bg-gray-50 dark:bg-slate-900 border border-gray-200 dark:border-dark-border rounded-lg text-xs dark:text-white focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 outline-none transition font-medium"
                  />
                </div>
                <div>
                  <label
                    class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1"
                    >To</label
                  >
                  <input
                    type="date"
                    v-model="dateTo"
                    class="w-full px-2 py-2 bg-gray-50 dark:bg-slate-900 border border-gray-200 dark:border-dark-border rounded-lg text-xs dark:text-white focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 outline-none transition font-medium"
                  />
                </div>
              </div>
              <div
                class="mt-4 flex justify-between items-center border-t dark:border-dark-border pt-3"
              >
                <button
                  @click="handleClearDateRange"
                  type="button"
                  class="text-[10px] font-bold text-red-500 dark:text-red-400 hover:text-red-700 transition"
                >
                  Clear
                </button>
                <button
                  @click="dateRangeOpen = false"
                  type="button"
                  class="px-4 py-2 bg-teal-500 text-white text-[10px] font-black rounded-lg hover:bg-teal-600 transition shadow-lg shadow-teal-500/20"
                >
                  Apply Range
                </button>
              </div>
            </div>
          </Transition>
        </div>
      </div>
    </div>

    <!-- Wrapper for the overlay positioning -->
    <div class="relative">
      <TableSpinner v-if="transaction.loading" :text="`Fetching orders...`" />

      <!-- Scrollable Table -->
      <div class="overflow-x-auto custom-scrollbar">
        <table class="w-full text-left min-w-[1000px]">
          <thead>
            <tr
              class="bg-gray-50/50 dark:bg-slate-800/20 text-[11px] uppercase tracking-widest text-gray-400 dark:text-slate-500 font-bold border-b border-gray-100 dark:border-dark-border"
            >
              <th class="px-6 py-4">Order ID</th>
              <th class="px-6 py-4">Customer</th>
              <th class="px-6 py-4">Order Date</th>
              <th class="px-6 py-4">Total Amount</th>
              <th class="px-6 py-4 text-center">Payment</th>
              <th class="px-6 py-4">Status</th>
              <th class="px-6 py-4 text-right">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100 dark:divide-dark-border">
            <tr
              v-for="order in transaction.orders"
              :key="order.order_number"
              class="hover:bg-gray-50/80 dark:hover:bg-slate-800/30 transition-colors group"
            >
              <td class="px-6 py-4 font-mono text-xs font-bold text-teal-600 dark:text-teal-400">
                {{ order.order_number }}
              </td>
              <td class="px-6 py-4">
                <div class="flex items-center gap-3">
                  <div
                    class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-xs font-bold"
                  >
                    {{ order.customer.charAt(0) }}
                  </div>
                  <span class="text-sm font-bold text-gray-900 dark:text-white">{{
                    order.customer
                  }}</span>
                </div>
              </td>
              <td class="px-6 py-4 text-xs text-gray-500 dark:text-slate-400">
                {{ order.created_at }}
              </td>
              <td class="px-6 py-4 font-black text-gray-900 dark:text-white">{{ order.total }}</td>
              <td class="px-6 py-4 text-center">
                <span
                  class="px-2 py-1 bg-gray-100 dark:bg-slate-800 rounded text-[10px] font-bold uppercase"
                  >{{ order.payment_method }}</span
                >
              </td>
              <td class="px-6 py-4">
                <span
                  class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider flex items-center gap-1.5 w-fit"
                  :class="`bg-${order.color}-100 text-${order.color}-700 dark:bg-${order.color}-500/10 dark:text-${order.color}-400`"
                >
                  <span
                    class="w-1.5 h-1.5 rounded-full"
                    :class="`bg-${order.color}-400 dark:bg-${order.color}-400`"
                  ></span>
                  {{ order.status }}
                </span>
              </td>
              <td class="px-6 py-4 text-right">
                <div class="flex items-center justify-end gap-2">
                  <button
                    @click="transaction.toggleModal('details', true, order)"
                    class="p-2 hover:bg-teal-50 dark:hover:bg-teal-500/10 text-gray-400 dark:text-slate-500 hover:text-teal-600 dark:hover:text-teal-400 rounded-lg transition"
                    type="button"
                  >
                    <EyeIcon class="w-4 h-4" />
                  </button>
                  <button
                    @click="transaction.toggleModal('delete', true, order)"
                    class="p-2 hover:bg-red-50 dark:hover:bg-red-500/10 text-gray-400 dark:text-slate-500 hover:text-red-600 dark:hover:text-red-400 rounded-lg transition"
                    type="button"
                  >
                    <TrashIcon class="w-4 h-4" />
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Pagination -->
    <Pagination v-if="transaction.meta" :meta="transaction.meta" @page-change="onPageChange" />
  </div>
</template>

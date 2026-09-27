import { defineStore } from 'pinia'
import axios from '@/utils/axios' // axios.js file
import { ref } from 'vue'
import type { Pagination, Stat } from '@/types/data-types'
import type { Customer } from '@/types/customer-types'
import { useFormatter } from '@/composables/useFormatter'
const { formatNumber } = useFormatter()

export const useCustomerStore = defineStore('customer', () => {
  const customers = ref<Customer[] | null>(null)
  const meta = ref<Pagination | null>(null)

  const loading = ref(false)
  const search = ref('')

  const stats = ref<Stat[]>([
    { label: 'Total Customers', val: '8,420', color: 'bg-teal-500', desc: 'Active directory' },
    { label: 'New This Week', val: '+420', color: 'bg-green-500', desc: 'Growth rate: 12%' },
    { label: 'Active Now', val: '1,102', color: 'bg-blue-500', desc: 'Currently online' },
    { label: 'Retention Rate', val: '94.2%', color: 'bg-purple-500', desc: 'Customer loyalty' },
  ])

  const fetchCustomers = async (
    searchTerm = search.value,
    page = meta.value?.current_page || 1,
    queries: { status?: string } | null = null,
  ) => {
    loading.value = true

    const params: Record<string, string | number | string[]> = {
      search: searchTerm,
      page,
    }

    if (queries) {
      if (queries.status) {
        params.status = queries.status
      }
    }

    const { data } = await axios.get(`/api/v1/customers`, { params })
    customers.value = data.data
    meta.value = data.meta

    loading.value = false
  }

  const fetchStatistics = async () => {
    const { data } = await axios.get(`/api/v1/customers/total`)

    const _stats = data.data

    if (stats.value[0]) stats.value[0].val = formatNumber(_stats.total) // only those has assigned roles
    if (stats.value[1]) stats.value[1].val = formatNumber(_stats.new)
    if (stats.value[2]) stats.value[2].val = formatNumber(_stats.active)
    if (stats.value[3]) stats.value[3].val = formatNumber(_stats.retained)
  }

  const updateCustomer = async (userId: number, status: number) => {
    loading.value = true
    try {
      const { data } = await axios.patch(`/api/v1/users/${userId}/status`, { is_active: status })
      return data
    } finally {
      loading.value = false
    }
  }

  return {
    customers,
    meta,
    loading,
    search,
    stats,
    fetchCustomers,
    fetchStatistics,
    updateCustomer,
  }
})

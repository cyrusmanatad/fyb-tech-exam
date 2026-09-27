import { defineStore } from 'pinia'
import axios from '@/utils/axios' // axios.js file
import { ref } from 'vue'
import type { Pagination, Stat } from '@/types/data-types'
import type { Role, User, UserForm } from '@/types/user-types'
import { useFormatter } from '@/composables/useFormatter'
import type { ROLES } from '@/types/enum'
const { formatNumber } = useFormatter()

export const useUserStore = defineStore('users', () => {
  const users = ref<User[] | null>(null)
  const meta = ref<Pagination | null>(null)
  const roles = ref<Role[] | null>(null)

  const isLoading = ref(false)
  const search = ref('')

  const stats = ref<Stat[]>([
    { label: 'Total Users', val: '12', color: 'bg-teal-500', desc: 'System-wide' },
    { label: 'Administrators', val: '3', color: 'bg-purple-500', desc: 'Full access' },
    { label: 'Staff / Support', val: '9', color: 'bg-blue-500', desc: 'Limited access' },
    { label: 'Active Sessions', val: '4', color: 'bg-green-500', desc: 'Logged in now' },
  ])

  const createUser = async (payload: UserForm) => {
    isLoading.value = true
    try {
      const { data } = await axios.post('/api/v1/users', payload)
      fetchUsers()
      return data
    } finally {
      isLoading.value = false
    }
  }

  const deleteUser = async (userId: number) => {
    isLoading.value = true
    try {
      const { data } = await axios.delete(`/api/v1/users/${userId}`)
      fetchUsers()
      return data
    } finally {
      isLoading.value = false
    }
  }

  const fetchUsers = async (
    searchTerm = search.value,
    page = meta.value?.current_page || 1,
    queries: { role?: string[]; status?: string } | null = null,
  ) => {
    isLoading.value = true

    const params: any = {
      search: searchTerm,
      page,
    }

    if (queries) {
      if (queries.status) {
        params.status = queries.status
      }
      if (queries.role) {
        params.category = queries.role
      }
    }

    const { data } = await axios.get(`/api/v1/users`, { params })
    users.value = data.data
    meta.value = data.meta

    isLoading.value = false
  }

  const fetchStatistics = async () => {
    const { data } = await axios.get(`/api/v1/users/total`)

    const _stats = data.data

    if (stats.value[0]) stats.value[0].val = formatNumber(_stats.total) // only those has assigned roles
    if (stats.value[1]) stats.value[1].val = formatNumber(_stats.admin)
    if (stats.value[2]) stats.value[2].val = formatNumber(_stats.non_admin)
    if (stats.value[3]) stats.value[3].val = '1' //formatNumber(_stats.active) // need to replace with current logged in users
  }

  const updateRole = async (userId: number, role: ROLES) => {
    isLoading.value = true
    try {
      const { data } = await axios.patch(`/api/v1/users/${userId}/role`, { role })
      return data
    } finally {
      isLoading.value = false
    }
  }

  return {
    users,
    roles,
    search,
    stats,
    isLoading,
    meta,
    fetchStatistics,
    fetchUsers,
    updateRole,
    createUser,
    deleteUser,
  }
})

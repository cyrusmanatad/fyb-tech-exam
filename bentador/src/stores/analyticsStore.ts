import { ref } from 'vue'
import { defineStore } from 'pinia'
import axios from '@/utils/axios' // axios.js file
import type { CategoryData, KpiData, RevenueData } from '@/types/analytics'

export const useAnalyticsStore = defineStore('analytics', () => {
  const revenue = ref<RevenueData | null>(null)
  const categories = ref<CategoryData | null>(null)
  const kpi = ref<KpiData | null>(null)
  const loading = ref(false)
  const categoryError = ref('')

  const fetchRevenue = async () => {
    loading.value = true
    try {
      const { data } = await axios.get('/api/v1/analytics/revenue')
      revenue.value = data.data
    } finally {
      loading.value = false
    }
  }

  const fetchCategories = async () => {
    loading.value = true
    categoryError.value = ''
    try {
      const { data } = await axios.get('/api/v1/analytics/categories')
      categories.value = data.data
    } catch {
      categoryError.value = 'Category sales did not load. Refresh the page to try again.'
    } finally {
      loading.value = false
    }
  }

  const fetchKpi = async () => {
    loading.value = true
    try {
      const { data } = await axios.get('/api/v1/analytics/kpi')
      kpi.value = data.data
    } finally {
      loading.value = false
    }
  }

  const fetchAll = async () => {
    await Promise.all([fetchRevenue(), fetchCategories(), fetchKpi()])
  }

  return {
    revenue,
    categories,
    kpi,
    loading,
    categoryError,
    fetchRevenue,
    fetchCategories,
    fetchKpi,
    fetchAll,
  }
})

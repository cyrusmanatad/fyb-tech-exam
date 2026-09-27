import type { Category } from '@/types/data-types'
import { defineStore } from 'pinia'
import axios from '@/utils/axios' // axios.js file
import { ref } from 'vue'

export const useCategoryStore = defineStore('category', () => {
  const categories = ref<Category[]>([])
  const loading = ref(false)
  const loaded = ref(false)

  const fetchCategories = async () => {
    if (loaded.value) return
    loading.value = true
    try {
      const { data } = await axios.get('/api/v1/categories')
      categories.value = data
      loaded.value = true
    } finally {
      loading.value = false
    }
  }

  const clearCategories = () => {
    categories.value = []
    loading.value = false
    loaded.value = false
  }

  return { categories, loading, loaded, clearCategories, fetchCategories }
})

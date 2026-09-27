import { defineStore } from 'pinia'
import axios from '@/utils/axios' // axios.js file
import { useCategoryStore } from './category'
import { ref } from 'vue'
import type { Credentials, PasswordForm, ProfileForm, User } from '@/types/auth'
import router from '@/router'

export const useAuthStore = defineStore('auth', () => {
  const user = ref<User | null>()

  const accessToken = ref('')
  const loading = ref(false)
  const status = ref(200)
  const loginError = ref('')

  const clearLoginError = () => {
    loginError.value = ''
  }

  const login = async (credentials: Credentials) => {
    clearLoginError()

    try {
      const { data } = await axios.post('/api/v1/auth/login', credentials)

      accessToken.value = data.authorization.access_token || null

      localStorage.setItem('auth_token', accessToken.value)

      axios.defaults.headers.common['Authorization'] = `Bearer ${accessToken.value}`

      await fetchUser()

      const redirect = router.currentRoute.value.query.redirect as string
      router.push(redirect || { name: 'order-entry' })
    } catch (error: unknown) {
      accessToken.value = ''
      const message = axios.isAxiosError(error) ? error.response?.data?.message : ''
      loginError.value =
        typeof message === 'string' && message !== ''
          ? message
          : 'Sign-in did not complete. Try again.'
      return false
    }
  }

  const fetchUser = async (force = false) => {
    loading.value = true
    try {
      if (user.value && !force) return

      const { data } = await axios.get('/api/v1/users/me')

      user.value = data.data
      status.value = 200
    } catch (error: unknown) {
      status.value = axios.isAxiosError(error) ? (error.response?.status ?? 500) : 500
      user.value = null
    } finally {
      loading.value = false
    }
  }

  const updateProfile = async (payload: ProfileForm) => {
    const { data } = await axios.put('/api/v1/profile', payload)
    user.value = data.data
    return data
  }

  const updatePassword = async (payload: PasswordForm) => {
    const { data } = await axios.put('/api/v1/profile/password', payload)
    return data
  }

  const hasRole = (role: string): boolean => {
    return user.value?.roles.includes(role) ?? false
  }

  const hasPermission = (permission: string[]): boolean => {
    return user.value?.permissions.some((p) => permission.includes(p)) ?? false
  }

  const refreshToken = async () => {
    try {
      const { data } = await axios.post('/api/v1/auth/refresh')
      accessToken.value = data.authorization.access_token
      return true
    } catch {
      accessToken.value = ''
      return false
    }
  }

  const logout = async () => {
    await axios.post('/api/v1/auth/logout')
    user.value = null

    const categories = useCategoryStore()
    categories.clearCategories()
    return true
  }

  return {
    user,
    accessToken,
    loading,
    status,
    loginError,
    clearLoginError,
    login,
    fetchUser,
    hasRole,
    hasPermission,
    refreshToken,
    logout,
    updateProfile,
    updatePassword,
  }
})

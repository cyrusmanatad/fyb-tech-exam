import { defineStore } from 'pinia'
import axios from '@/utils/axios' // axios.js file
import { useCategoryStore } from './category'
import { ref } from 'vue'
import type { Credentials, PasswordForm, ProfileForm, RegisterPayload, User } from '@/types/auth'
import router from '@/router'

export const useAuthStore = defineStore('auth', () => {
  const user = ref<User | null>()

  const accessToken = ref('')
  const loading = ref(false)
  const status = ref(200)
  const loginError = ref('')
  const registerError = ref('')
  const registerFieldErrors = ref<Record<string, string[]>>({})

  const clearLoginError = () => {
    loginError.value = ''
  }

  const clearRegisterError = () => {
    registerError.value = ''
    registerFieldErrors.value = {}
  }

  const storeToken = (token: string) => {
    accessToken.value = token
    localStorage.setItem('auth_token', token)
    axios.defaults.headers.common['Authorization'] = `Bearer ${token}`
  }

  const login = async (credentials: Credentials, options?: { redirect?: boolean }) => {
    clearLoginError()

    try {
      const { data } = await axios.post('/api/v1/auth/login', credentials)

      storeToken(data.authorization.access_token || '')

      await fetchUser()

      if (options?.redirect === false) {
        return true
      }

      const redirect = router.currentRoute.value.query.redirect as string
      router.push(redirect || { name: 'order-entry' })
      return true
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

  const register = async (payload: RegisterPayload, options?: { redirect?: boolean }) => {
    clearRegisterError()

    try {
      const { data } = await axios.post('/api/v1/auth/register', payload)

      storeToken(data.authorization.access_token || '')

      await fetchUser()

      if (options?.redirect !== false) {
        router.push({ name: 'order-entry' })
      }

      return true
    } catch (error: unknown) {
      accessToken.value = ''

      if (axios.isAxiosError(error) && error.response?.status === 422) {
        const errors = error.response.data?.errors
        registerFieldErrors.value =
          errors && typeof errors === 'object' ? (errors as Record<string, string[]>) : {}
        const message = error.response.data?.message
        registerError.value =
          typeof message === 'string' && message !== '' ? message : 'Check the form and try again.'
      } else {
        registerError.value = 'Account was not created. Try again.'
      }

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
    registerError,
    registerFieldErrors,
    clearLoginError,
    clearRegisterError,
    login,
    register,
    fetchUser,
    hasRole,
    hasPermission,
    refreshToken,
    logout,
    updateProfile,
    updatePassword,
  }
})

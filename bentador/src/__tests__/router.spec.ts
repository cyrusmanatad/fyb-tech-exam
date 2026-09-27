import { describe, it, expect, beforeEach } from 'vitest'
import { createPinia, setActivePinia } from 'pinia'
import router from '@/router'

describe('public shop route', () => {
  beforeEach(() => {
    localStorage.clear()
    setActivePinia(createPinia())
  })

  it('opens the order page at / without a token', async () => {
    await router.push('/')
    await router.isReady()

    expect(router.currentRoute.value.name).toBe('order-entry')
    expect(router.currentRoute.value.path).toBe('/')
  })

  it('sends /order-entry to the shop', async () => {
    await router.push('/order-entry')
    await router.isReady()

    expect(router.currentRoute.value.name).toBe('order-entry')
    expect(router.currentRoute.value.path).toBe('/')
  })

  it('sends a guest away from the admin dashboard', async () => {
    await router.push('/analytics')
    await router.isReady()

    expect(router.currentRoute.value.name).toBe('login')
  })
})

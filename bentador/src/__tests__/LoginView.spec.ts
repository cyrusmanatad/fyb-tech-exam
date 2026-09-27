import { describe, it, expect, vi, beforeEach } from 'vitest'
import { flushPromises, mount } from '@vue/test-utils'
import { createPinia } from 'pinia'
import { createMemoryHistory, createRouter } from 'vue-router'

import LoginView from '@/views/LoginView.vue'
import axios from '@/utils/axios'

vi.mock('@/utils/axios', () => ({
  default: {
    post: vi.fn(),
    get: vi.fn(),
    defaults: { headers: { common: {} as Record<string, string> } },
    isAxiosError: (error: unknown) =>
      Boolean(error && typeof error === 'object' && 'isAxiosError' in error && error.isAxiosError),
  },
}))

const rejectedLogin = {
  isAxiosError: true,
  response: {
    status: 401,
    data: {
      message: 'The email or password is incorrect. Check both and try again.',
    },
  },
}

const mountLogin = async () => {
  const router = createRouter({
    history: createMemoryHistory(),
    routes: [
      { path: '/login', name: 'login', component: LoginView },
      { path: '/order-entry', name: 'order-entry', component: { template: '<div />' } },
      { path: '/analytics', name: 'analytics', component: { template: '<div />' } },
    ],
  })

  await router.push('/login')

  const wrapper = mount(LoginView, {
    global: {
      plugins: [createPinia(), router],
    },
  })

  return { wrapper, router }
}

describe('LoginView', () => {
  beforeEach(() => {
    vi.mocked(axios.post).mockReset()
  })

  it('shows the API message when the credentials are rejected', async () => {
    vi.mocked(axios.post).mockRejectedValue(rejectedLogin)

    const { wrapper, router } = await mountLogin()

    expect(wrapper.find('[role="alert"]').exists()).toBe(false)

    await wrapper.find('input[type="email"]').setValue('person@bentadoor.com')
    await wrapper.find('input[type="password"]').setValue('wrong-password')
    await wrapper.find('form').trigger('submit')
    await flushPromises()

    expect(wrapper.get('[role="alert"]').text()).toBe(
      'The email or password is incorrect. Check both and try again.',
    )
    expect(router.currentRoute.value.name).toBe('login')

    await wrapper.find('input[type="password"]').setValue('another-try')

    expect(wrapper.find('[role="alert"]').exists()).toBe(false)
  })
})

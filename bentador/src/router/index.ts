import { createRouter, createWebHistory } from 'vue-router'
import AppLayout from '@/components/layouts/AppLayout.vue'
import AnalyticsView from '@/views/AnalyticsView.vue'
import OrdersView from '@/views/OrdersView.vue'
import CustomersView from '@/views/CustomersView.vue'
import InboxView from '@/views/InboxView.vue'
import UsersView from '@/views/UsersView.vue'
import RolesPermissionView from '@/views/RolesPermissionView.vue'
import LoginView from '@/views/LoginView.vue'
import ProductsView from '@/views/ProductsView.vue'
import OrderEntryView from '@/views/OrderEntryView.vue'
import ProfileSettingsView from '@/views/ProfileSettingsView.vue'
import AccountSettingsView from '@/views/AccountSettingsView.vue'
import { useAuthStore } from '@/stores/auth'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/login',
      name: 'login',
      component: LoginView,
      meta: { guestOnly: true },
    },
    {
      path: '/',
      name: 'order-entry',
      component: OrderEntryView,
      meta: { title: 'Order Entry' },
    },
    {
      path: '/order-entry',
      redirect: { name: 'order-entry' },
    },
    {
      path: '/',
      component: AppLayout,
      meta: { requiresAuth: true },
      children: [
        {
          path: 'analytics',
          name: 'analytics',
          component: AnalyticsView,
          meta: {
            permissions: ['view analytics'],
          },
        },
        {
          path: 'products',
          name: 'products',
          component: ProductsView,
          meta: {
            permissions: ['view products'],
          },
        },
        {
          path: 'orders',
          name: 'orders',
          component: OrdersView,
          meta: {
            permissions: ['view orders'],
          },
        },
        {
          path: 'customers',
          name: 'customers',
          component: CustomersView,
          meta: {
            permissions: ['view customers'],
          },
        },
        {
          path: 'inbox',
          name: 'inbox',
          component: InboxView,
          meta: {
            permissions: ['view inbox'],
          },
        },
        {
          path: 'users',
          name: 'users',
          component: UsersView,
          meta: {
            permissions: ['view users'],
          },
        },
        {
          path: 'roles-permission',
          name: 'roles-permission',
          component: RolesPermissionView,
          meta: {
            permissions: ['view roles-permission'],
          },
        },
        {
          path: 'profile',
          name: 'profile-settings',
          component: ProfileSettingsView,
          meta: {
            title: 'Profile Settings',
          },
        },
        {
          path: 'account',
          name: 'account-settings',
          component: AccountSettingsView,
          meta: {
            title: 'Account Settings',
          },
        },
      ],
    },
  ],
})

router.beforeEach(async (to) => {
  const auth = useAuthStore()
  const token = localStorage.getItem('auth_token')

  // A guest has no token, so the shop must not call /users/me.
  if (!auth.user && token) {
    await auth.fetchUser()
  }

  if (to.meta.requiresAuth && !auth.user) {
    return { name: 'login', query: { redirect: to.fullPath } }
  }

  // Role check. Meta fields are widened because vue-router types them as {}.
  const requiredRoles = to.meta.roles as unknown as string[] | undefined
  if (requiredRoles?.length) {
    const hasRole = requiredRoles.some((role) => auth.user?.roles.includes(role))

    if (!hasRole) {
      return { name: 'order-entry' }
    }
  }

  // Permission check
  const requiredPermissions = to.meta.permissions as unknown as string[] | undefined
  if (requiredPermissions?.length) {
    const hasPermission = requiredPermissions.every((permission) =>
      auth.user?.permissions.includes(permission),
    )

    if (!hasPermission) {
      return { name: 'order-entry' }
    }
  }

  return true
})

router.afterEach(async (to) => {
  document.title = `Benta Door: ${to.meta.title ?? 'Dashboard'}`
})

export default router

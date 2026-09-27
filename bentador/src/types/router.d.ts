import 'vue-router'

declare module 'vue-router' {
  interface RouteMeta {
    requiresAuth?: boolean
    permissions?: string[] // required permissions
    roles?: string[] // required roles
    guestOnly?: boolean // only for non-authenticated
    title?: string
  }
}

import type { ROLES } from './enum'

export interface User {
  id: number
  name: string
  email: string
  login: string
  status: string
  color: string
  roles: ROLES[]
  permissions: string[]
}

export interface RoleUser {
  id: number
  name: string
  avatar: string
}

export interface Role {
  id: number
  name: string
  description: string
  color: string
  users_count: number
  users: RoleUser[]
  permissions: string[]
}

export interface RolePermissions {
  [module: string]: string[] // { Products: ['view products', 'edit products'] }
}

export interface UserForm {
  name: string
  email: string
  role: string
  status: 'active' | 'inactive'
  // password: string // initial password for new user
}

export interface UserFormErrors {
  name?: string[]
  email?: string[]
  role?: string[]
  status?: string[]
  // password?: string[]
}

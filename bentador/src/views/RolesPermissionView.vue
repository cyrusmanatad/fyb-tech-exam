<script setup lang="ts">
import { handleError, onMounted, ref } from 'vue'
import { useUiStore } from '@/stores/ui'
import {
  Bars3Icon,
  HomeIcon,
  PlusIcon,
  ChevronDownIcon,
  TrashIcon,
  CheckIcon,
  XMarkIcon,
  ShieldExclamationIcon,
} from '@heroicons/vue/24/outline'
import BaseModal from '@/components/common/BaseModal.vue'
import { useRoleStore } from '@/stores/roleStore'
import type { Role } from '@/types/user-types'

const uiStore = useUiStore()
const roleStore = useRoleStore()

const roleModal = ref(false)
const deleteModal = ref(false)

const formRole = ref('')
const formDescription = ref('')
const formPermissions = ref<string[]>([])

const newRole = ref(false)

// Build permission string from action + module
// e.g. action='Edit', module='Products' -> 'edit products'
const buildPermissionKey = (action: string, module: string): string => {
  return `${action.toLowerCase()} ${module.toLowerCase()}`
}

const permissionExists = (action: string, module: string): boolean => {
  const key = buildPermissionKey(action, module)
  return roleStore?.permissions?.includes(key)
}

// Check if permission is active — from LOCAL state
const hasPermission = (action: string, module: string): boolean => {
  const key = buildPermissionKey(action, module)
  return formPermissions.value.includes(key)
}

// Toggle permission — mutates LOCAL state only
const togglePermission = (action: string, module: string): void => {
  const key = `${action.toLowerCase()} ${module.toLowerCase()}`
  const index = formPermissions.value.indexOf(key)
  if (index === -1) {
    formPermissions.value.push(key)
  } else {
    formPermissions.value.splice(index, 1)
  }
}

const handleCreateRule = () => {
  formRole.value = ''
  formDescription.value = ''
  formPermissions.value = []

  newRole.value = true

  roleModal.value = true
  roleStore.resetForm()
}

const handleEditRule = (role: Role) => {
  formRole.value = role.name
  formDescription.value = role.description
  formPermissions.value = [...role.permissions]

  newRole.value = false

  roleModal.value = true
  roleStore.selectedRole = role
}

const handleDeleteRule = (role: Role) => {
  deleteModal.value = true
  roleStore.selectedRole = role
}

const handleSave = async () => {
  await roleStore.saveRole(
    {
      name: formRole.value,
      description: formDescription.value,
      permissions: formPermissions.value,
    },
    newRole.value ? 'create' : 'update',
  )
  roleModal.value = false
  roleStore.resetForm()
}

const handleDelete = async () => {
  await roleStore.deleteRole()
  deleteModal.value = false
}

const handleClose = () => {
  roleModal.value = false
  roleStore.resetForm()
}

onMounted(() => {
  roleStore.fetchRoles()
  roleStore.fetchPermissions()
})
</script>

<template>
  <div class="p-4 md:p-8">
    <!-- HEADER -->
    <header
      class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4"
    >
      <div class="flex items-center gap-4">
        <button
          @click="uiStore.setSidebar(true)"
          class="lg:hidden p-2 bg-white dark:bg-dark-card border border-gray-200 dark:border-dark-border rounded-xl"
          type="button"
        >
          <Bars3Icon class="w-6 h-6" />
        </button>
        <div>
          <nav class="flex items-center gap-2 text-[12px] text-gray-400 mb-1">
            <HomeIcon class="w-3 h-3" />
            <span>/</span>
            <span class="text-teal-600 dark:text-teal-400 font-medium">Roles & Permissions</span>
          </nav>
          <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Security Settings</h1>
          <p class="text-gray-500 dark:text-slate-400 text-sm">
            Manage access levels and module permissions
          </p>
        </div>
      </div>
      <button
        @click="handleCreateRule"
        class="bg-teal-500 hover:bg-teal-600 text-white px-5 py-2.5 rounded-xl text-sm font-bold flex items-center justify-center gap-2 shadow-lg shadow-teal-500/20 transition-all active:scale-95"
        type="button"
      >
        <PlusIcon class="w-4 h-4" /> Create Role
      </button>
    </header>

    <!-- ROLES LIST -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 mb-8">
      <div
        v-for="role in roleStore.roles"
        :key="role.id"
        class="bg-white dark:bg-dark-card p-6 rounded-3xl border border-gray-100 dark:border-dark-border shadow-sm hover:shadow-xl transition-all group"
      >
        <div class="flex justify-between items-start mb-4">
          <div class="flex items-center gap-2">
            <span class="w-2 h-2 rounded-full" :class="`bg-${role.color}-500`"></span>
            <h3 class="text-lg font-black text-gray-900 dark:text-white">{{ role.name }}</h3>
          </div>
          <div class="flex items-center -space-x-2">
            <img
              v-for="user in role.users"
              :key="user.id"
              :src="user.avatar"
              class="w-7 h-7 rounded-full border-2 border-white dark:border-dark-card shadow-sm"
              :alt="user.name"
            />
            <div
              class="w-7 h-7 rounded-full bg-gray-100 dark:bg-slate-800 border-2 border-white dark:border-dark-card flex items-center justify-center text-[8px] font-black"
            >
              <span v-if="role.users_count > 4">+{{ role.users_count - 3 }}</span>
              <span v-else>{{ role.users_count }}</span>
            </div>
          </div>
        </div>
        <p class="text-xs text-gray-500 dark:text-slate-400 mb-6">{{ role.description }}</p>

        <div class="flex flex-wrap gap-1 mb-4">
          <span
            v-for="permission in role.permissions.slice(0, 4)"
            :key="permission"
            class="px-2 py-0.5 bg-gray-100 dark:bg-slate-800 text-gray-500 dark:text-slate-400 text-[9px] font-black uppercase tracking-wider rounded-lg"
          >
            {{ permission }}
          </span>
          <span
            v-if="role.permissions.length > 4"
            class="px-2 py-0.5 bg-teal-50 dark:bg-teal-500/10 text-teal-600 dark:text-teal-400 text-[9px] font-black uppercase tracking-wider rounded-lg"
          >
            +{{ role.permissions.length - 4 }} more
          </span>
        </div>

        <div class="flex gap-2">
          <button
            @click="handleEditRule(role)"
            class="flex-1 py-2 bg-gray-50 dark:bg-slate-800/50 hover:bg-teal-50 dark:hover:bg-teal-500/10 text-gray-600 dark:text-slate-400 hover:text-teal-600 dark:hover:text-teal-400 text-[10px] font-black uppercase tracking-widest rounded-xl transition"
            type="button"
          >
            Edit Role
          </button>
          <button
            @click="handleDeleteRule(role)"
            class="p-2 bg-gray-50 dark:bg-slate-800/50 hover:bg-red-50 dark:hover:bg-red-500/10 text-gray-400 hover:text-red-600 rounded-xl transition"
            type="button"
          >
            <TrashIcon class="w-4 h-4" />
          </button>
        </div>
      </div>
    </div>

    <!-- PERMISSION GUIDE SECTION -->
    <div
      class="bg-white dark:bg-dark-card rounded-2xl border border-gray-200 dark:border-dark-border shadow-sm overflow-hidden"
    >
      <div class="p-6 border-b dark:border-dark-border">
        <h3 class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-widest">
          Global Permissions Overview
        </h3>
      </div>
      <div class="p-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
        <div v-for="mod in roleStore.modules" :key="mod" class="space-y-3">
          <h4
            class="text-[10px] font-black text-gray-400 dark:text-slate-500 uppercase tracking-widest"
          >
            {{ mod }}
          </h4>
          <div class="space-y-2">
            <div
              v-for="perm in roleStore.actions"
              :key="perm"
              class="flex items-center justify-between text-xs"
            >
              <span class="text-gray-600 dark:text-slate-400">{{ perm }} {{ mod }}</span>
              <div class="w-4 h-4 rounded bg-teal-500/10 flex items-center justify-center">
                <CheckIcon class="w-2.5 h-2.5 text-teal-600" />
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modals -->
    <BaseModal :show="roleModal" @close="handleClose" maxWidth="max-w-2xl">
      <div
        class="p-6 border-b dark:border-dark-border flex justify-between items-center bg-gray-50/50 dark:bg-slate-800/20"
      >
        <div>
          <h2 class="text-xl font-black text-gray-900 dark:text-white">
            {{ roleStore.selectedRole?.name ? 'Edit Role' : 'Create Role' }}
          </h2>
          <p class="text-xs text-gray-500 dark:text-slate-400">
            Configure module-level permissions
          </p>
        </div>
        <button
          @click="roleModal = false"
          class="p-2 hover:bg-gray-200 dark:hover:bg-slate-800 rounded-full transition text-gray-400"
          type="button"
        >
          <XMarkIcon class="w-5 h-5" />
        </button>
      </div>

      <div class="p-6 space-y-6 overflow-y-auto custom-scrollbar">
        <div>
          <label
            class="block text-[10px] font-black text-gray-400 dark:text-slate-500 uppercase tracking-widest mb-1.5"
            >Role Name</label
          >
          <input
            type="text"
            v-model="formRole"
            placeholder="e.g. Sales Manager"
            class="w-full px-4 py-3 bg-gray-50 dark:bg-slate-900 border border-gray-200 dark:border-dark-border rounded-xl dark:text-white focus:ring-2 focus:ring-teal-500/20 outline-none transition font-bold"
          />
        </div>

        <div>
          <label
            class="block text-[10px] font-black text-gray-400 dark:text-slate-500 uppercase tracking-widest mb-1.5"
            >Description</label
          >
          <input
            type="text"
            v-model="formDescription"
            placeholder="e.g. Can manage shop items and orders"
            class="w-full px-4 py-3 bg-gray-50 dark:bg-slate-900 border border-gray-200 dark:border-dark-border rounded-xl dark:text-white focus:ring-2 focus:ring-teal-500/20 outline-none transition font-bold"
          />
        </div>

        <div class="space-y-4">
          <label
            class="block text-[10px] font-black text-gray-400 dark:text-slate-500 uppercase tracking-widest"
            >Module Permissions</label
          >

          <div
            v-for="module in roleStore.modules"
            :key="module"
            class="p-4 bg-gray-50 dark:bg-slate-900/50 rounded-2xl flex flex-col sm:flex-row sm:items-center justify-between gap-4"
          >
            <span class="text-sm font-black text-gray-700 dark:text-slate-300">{{ module }}</span>
            <div class="flex flex-wrap gap-4">
              <label
                v-for="action in roleStore.actions"
                :key="action"
                class="flex items-center gap-2 cursor-pointer group"
                :class="{
                  // Dim if permission doesn't exist for this module/action combo
                  'opacity-30 pointer-events-none': !permissionExists(action, module),
                }"
              >
                <div class="relative flex items-center">
                  <input
                    type="checkbox"
                    class="sr-only peer"
                    :checked="hasPermission(action, module)"
                    :disabled="!permissionExists(action, module)"
                    @change="togglePermission(action, module)"
                  />
                  <div
                    class="w-10 h-5 bg-gray-200 dark:bg-slate-700 peer-checked:bg-teal-500 rounded-full transition-colors"
                  ></div>
                  <div
                    class="absolute left-0.5 top-0.5 w-4 h-4 bg-white rounded-full transition-transform peer-checked:translate-x-5 shadow-sm"
                  ></div>
                </div>
                <span
                  class="text-[10px] font-black uppercase text-gray-500 dark:text-slate-400 group-hover:text-teal-600 transition"
                  >{{ action }}</span
                >
              </label>
            </div>
          </div>
        </div>
      </div>

      <div
        class="p-6 bg-gray-50 dark:bg-slate-800/20 border-t dark:border-dark-border flex justify-end gap-3"
      >
        <button
          @click="handleClose"
          class="px-5 py-2.5 text-sm font-bold text-gray-500 dark:text-slate-400 hover:bg-gray-200 dark:hover:bg-slate-800 rounded-xl transition"
          type="button"
        >
          Cancel
        </button>
        <button
          @click="handleSave"
          :disabled="roleStore.saving"
          class="px-6 py-2.5 text-sm font-bold text-white bg-teal-500 hover:bg-teal-600 rounded-xl shadow-lg transition"
          type="button"
        >
          {{ roleStore.saving ? 'Saving...' : 'Save Permissions' }}
        </button>
      </div>
    </BaseModal>

    <BaseModal :show="deleteModal" @close="deleteModal = false" maxWidth="max-w-sm">
      <div class="p-8 text-center">
        <div
          class="w-20 h-20 bg-red-100 dark:bg-red-500/10 text-red-600 dark:text-red-400 rounded-full flex items-center justify-center mx-auto mb-6"
        >
          <ShieldExclamationIcon class="w-10 h-10" />
        </div>
        <h2 class="text-xl font-black text-gray-900 dark:text-white mb-2">Delete Role?</h2>
        <p class="text-sm text-gray-500 dark:text-slate-400 mb-8 leading-relaxed">
          Deleting the
          <span class="font-bold text-gray-900 dark:text-white">{{
            roleStore.selectedRole?.name
          }}</span>
          role may affect
          <span class="font-bold">{{ roleStore.selectedRole?.users_count }}</span> active users.
        </p>
        <div class="flex flex-col gap-3">
          <button
            @click="handleDelete"
            :disabled="roleStore.saving"
            class="w-full py-3.5 bg-red-500 hover:bg-red-600 text-white font-black rounded-2xl shadow-xl transition"
            type="button"
          >
            Yes, Remove Role
          </button>
          <button
            @click="deleteModal = false"
            class="w-full py-3 text-gray-400 dark:text-slate-500 font-bold hover:text-gray-900 dark:hover:text-white transition"
            type="button"
          >
            Cancel
          </button>
        </div>
      </div>
    </BaseModal>
  </div>
</template>

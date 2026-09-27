<script setup lang="ts">
import { nextTick, ref, watch } from 'vue'
import BaseModal from '@/components/common/BaseModal.vue'
import { useAuthStore } from '@/stores/auth'
import { EnvelopeIcon, LockClosedIcon, UserIcon, XMarkIcon } from '@heroicons/vue/24/outline'

const props = defineProps<{
  show: boolean
  purpose?: 'checkout' | 'account'
}>()

const emit = defineEmits<{
  (e: 'close'): void
  (e: 'success'): void
}>()

const auth = useAuthStore()

const mode = ref<'login' | 'register'>('login')
const name = ref('')
const email = ref('')
const password = ref('')
const isSubmitting = ref(false)

const fieldError = (key: string) => auth.registerFieldErrors[key]?.[0] ?? ''

const nameInput = ref<HTMLInputElement | null>(null)
const emailInput = ref<HTMLInputElement | null>(null)

watch(
  () => props.show,
  async (open) => {
    if (!open) return
    await nextTick()
    const field = mode.value === 'register' ? nameInput.value : emailInput.value
    field?.focus()
  },
)

const switchMode = (next: 'login' | 'register') => {
  mode.value = next
  auth.clearLoginError()
  auth.clearRegisterError()
}

const close = () => {
  auth.clearLoginError()
  auth.clearRegisterError()
  emit('close')
}

const submit = async () => {
  isSubmitting.value = true

  const success =
    mode.value === 'login'
      ? await auth.login({ email: email.value, password: password.value }, { redirect: false })
      : await auth.register(
          { name: name.value, email: email.value, password: password.value },
          { redirect: false },
        )

  isSubmitting.value = false

  if (success) {
    name.value = ''
    email.value = ''
    password.value = ''
    emit('success')
  }
}
</script>

<template>
  <BaseModal :show="show" max-width="max-w-md" z-index-class="z-[140]" @close="close">
    <div class="flex flex-col" role="dialog" aria-modal="true" aria-labelledby="auth-modal-title">
      <div
        class="px-6 py-5 border-b border-gray-100 dark:border-dark-border flex items-center justify-between"
      >
        <div>
          <h2 id="auth-modal-title" class="text-lg font-black text-gray-900 dark:text-white tracking-tight">
            {{
              mode === 'register'
                ? 'Create an account'
                : props.purpose === 'account'
                  ? 'Sign in'
                  : 'Sign in to check out'
            }}
          </h2>
          <p class="text-xs text-gray-500 dark:text-slate-400 mt-1">
            Your cart stays on this device.
          </p>
        </div>
        <button
          type="button"
          class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-white transition"
          @click="close"
        >
          <XMarkIcon class="w-6 h-6" />
          <span class="sr-only">Close</span>
        </button>
      </div>

      <div class="px-6 pt-5">
        <div class="grid grid-cols-2 gap-2 p-1 bg-gray-100 dark:bg-slate-800 rounded-2xl">
          <button
            type="button"
            class="min-h-11 py-2.5 rounded-xl text-sm font-bold transition"
            :class="
              mode === 'login'
                ? 'bg-white dark:bg-dark-card text-gray-900 dark:text-white shadow-sm'
                : 'text-gray-500 dark:text-slate-400'
            "
            @click="switchMode('login')"
          >
            Sign in
          </button>
          <button
            type="button"
            class="min-h-11 py-2.5 rounded-xl text-sm font-bold transition"
            :class="
              mode === 'register'
                ? 'bg-white dark:bg-dark-card text-gray-900 dark:text-white shadow-sm'
                : 'text-gray-500 dark:text-slate-400'
            "
            @click="switchMode('register')"
          >
            Create account
          </button>
        </div>
      </div>

      <form class="p-6 space-y-4" @submit.prevent="submit">
        <div v-if="mode === 'register'">
          <label
            for="checkout-name"
            class="block text-[10px] font-black text-gray-400 dark:text-slate-500 uppercase tracking-widest mb-2 ml-1"
            >Name</label
          >
          <div class="relative">
            <UserIcon class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
            <input
              id="checkout-name"
              ref="nameInput"
              v-model="name"
              type="text"
              required
              autocomplete="name"
              class="w-full pl-12 pr-4 py-3.5 bg-gray-50 dark:bg-slate-900 border border-gray-200 dark:border-dark-border rounded-2xl text-sm font-bold focus:ring-4 focus:ring-teal-500/10 focus:border-teal-500 dark:text-white outline-none"
              @input="auth.clearRegisterError()"
            />
          </div>
          <p v-if="fieldError('name')" class="mt-2 text-sm text-red-500">{{ fieldError('name') }}</p>
        </div>

        <div>
          <label
            for="checkout-email"
            class="block text-[10px] font-black text-gray-400 dark:text-slate-500 uppercase tracking-widest mb-2 ml-1"
            >Email</label
          >
          <div class="relative">
            <EnvelopeIcon class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
            <input
              id="checkout-email"
              ref="emailInput"
              v-model="email"
              type="email"
              required
              autocomplete="username"
              class="w-full pl-12 pr-4 py-3.5 bg-gray-50 dark:bg-slate-900 border border-gray-200 dark:border-dark-border rounded-2xl text-sm font-bold focus:ring-4 focus:ring-teal-500/10 focus:border-teal-500 dark:text-white outline-none"
              @input="mode === 'login' ? auth.clearLoginError() : auth.clearRegisterError()"
            />
          </div>
          <p v-if="mode === 'register' && fieldError('email')" class="mt-2 text-sm text-red-500">
            {{ fieldError('email') }}
          </p>
        </div>

        <div>
          <label
            for="checkout-password"
            class="block text-[10px] font-black text-gray-400 dark:text-slate-500 uppercase tracking-widest mb-2 ml-1"
            >Password</label
          >
          <div class="relative">
            <LockClosedIcon
              class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"
            />
            <input
              id="checkout-password"
              v-model="password"
              type="password"
              required
              :minlength="mode === 'register' ? 8 : undefined"
              :autocomplete="mode === 'login' ? 'current-password' : 'new-password'"
              class="w-full pl-12 pr-4 py-3.5 bg-gray-50 dark:bg-slate-900 border border-gray-200 dark:border-dark-border rounded-2xl text-sm font-bold focus:ring-4 focus:ring-teal-500/10 focus:border-teal-500 dark:text-white outline-none"
              @input="mode === 'login' ? auth.clearLoginError() : auth.clearRegisterError()"
            />
          </div>
          <p v-if="mode === 'register' && fieldError('password')" class="mt-2 text-sm text-red-500">
            {{ fieldError('password') }}
          </p>
        </div>

        <p
          v-if="mode === 'login' && auth.loginError"
          role="alert"
          class="text-sm text-red-500"
        >
          {{ auth.loginError }}
        </p>
        <p
          v-else-if="mode === 'register' && auth.registerError && !fieldError('email') && !fieldError('password') && !fieldError('name')"
          role="alert"
          class="text-sm text-red-500"
        >
          {{ auth.registerError }}
        </p>

        <button
          type="submit"
          :disabled="isSubmitting"
          class="w-full py-4 bg-teal-500 hover:bg-teal-600 text-white font-black rounded-2xl shadow-xl shadow-teal-500/30 transition active:scale-95 disabled:opacity-70"
        >
          {{ isSubmitting ? 'Please wait…' : mode === 'login' ? 'Sign in' : 'Create account' }}
        </button>
      </form>
    </div>
  </BaseModal>
</template>

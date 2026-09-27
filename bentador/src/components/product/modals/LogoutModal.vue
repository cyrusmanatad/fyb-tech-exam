<script setup lang="ts">
import { useRouter } from 'vue-router'
import { useProductStore } from '@/stores/products'
import BaseModal from '@/components/common/BaseModal.vue'
import { ArrowRightStartOnRectangleIcon, ArrowRightIcon } from '@heroicons/vue/24/outline'
import { useAuthStore } from '@/stores/auth'

const productStore = useProductStore()
const router = useRouter()
const auth = useAuthStore()

const logout = async () => {
  auth.loading = true
  await auth.logout()

  productStore.toggleModal('logout', false)
  router.push({ name: 'login' })
}
</script>

<template>
  <BaseModal
    :show="productStore.modals.logout"
    @close="productStore.toggleModal('logout', false)"
    maxWidth="max-w-sm"
  >
    <div class="p-8 text-center border dark:border-dark-border">
      <div
        class="w-20 h-20 bg-orange-100 dark:bg-orange-500/10 text-orange-600 dark:text-orange-400 rounded-full flex items-center justify-center mx-auto mb-6 shadow-inner"
      >
        <ArrowRightStartOnRectangleIcon class="w-10 h-10" />
      </div>
      <h2 class="text-xl font-black text-gray-900 dark:text-white mb-2">Ready to Leave?</h2>
      <p class="text-sm text-gray-500 dark:text-slate-400 mb-8 leading-relaxed">
        Select "Logout" below if you are ready to end your current session. You will need to sign in
        again to access the dashboard.
      </p>
      <div class="flex flex-col gap-3">
        <button
          @click="logout"
          :disabled="auth.loading"
          class="w-full py-3.5 bg-teal-500 hover:bg-teal-600 text-white text-center font-black rounded-2xl shadow-xl shadow-teal-500/30 transition active:scale-95 disabled:opacity-70 disabled:cursor-not-allowed flex items-center justify-center gap-3"
          type="button"
        >
          <div v-if="!auth.loading" class="flex items-center gap-2">
            <span>Logout Now</span>
            <ArrowRightIcon class="w-4 h-4" />
          </div>
          <div v-else class="flex items-center gap-2">
            <div
              class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"
            ></div>
            <span>Logging you out...</span>
          </div>
        </button>
        <button
          v-show="!auth.loading"
          @click="productStore.toggleModal('logout', false)"
          class="w-full py-3 text-gray-400 dark:text-slate-500 font-bold hover:text-gray-900 dark:hover:text-white transition"
          type="button"
        >
          Cancel
        </button>
      </div>
    </div>
  </BaseModal>
</template>

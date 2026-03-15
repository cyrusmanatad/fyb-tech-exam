<script setup>
import { useAuthStore } from "@/stores/auth";
import { onMounted, ref, watch } from "vue";
import { RouterLink, useRouter } from "vue-router";
import PulseLoader from "vue-spinner/src/PulseLoader.vue";

const auth = useAuthStore();
const router = useRouter();

const onlogout = ref(false);

onMounted(() => {
  auth.loading = true;

  watch(
    [() => auth.user, () => auth.status],
    ([user, status]) => {
      if (user !== undefined || status !== null) {
        auth.loading = false
      }
    },
    { immediate: true }
  )
});

const logout = async () => {
  onlogout.value = true;
  const success = await auth.logout();
  onlogout.value = false;

  if (success) {
    router.push({ name: "login" });
  }
};
</script>

<template>
  <header class="sticky top-0 z-50 bg-white shadow-sm">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
      <div class="flex h-16 items-center justify-between">
        <div class="shrink-0">
          <a href="/" class="text-2xl font-bold text-blue-600"> Product Management </a>
        </div>
        <nav class="hidden md:flex md:space-x-8">
          <RouterLink
            to="/"
            class="px-3 py-2 text-sm font-medium text-gray-500 hover:text-gray-700"
          >
            Home
          </RouterLink>
          <RouterLink
            to="/products"
            class="px-3 py-2 text-sm font-medium text-gray-500 hover:text-gray-700"
          >
            Products
          </RouterLink>
          <RouterLink
            to="/forums"
            class="px-3 py-2 text-sm font-medium text-gray-500 hover:text-gray-700"
          >
            Forums
          </RouterLink>
        </nav>
        <div class="flex items-center">
          <div>
            <p>
              <span
                v-if="auth.loading"
                class="inline-block mt-1.5 h-4 w-32 rounded-md bg-gray-200 shadow-sm animate-pulse"
              ></span>
              <span v-else-if="auth.user !== null" class="text-sm font-medium text-gray-700">
                Hello, {{ auth.user.name || "User!" }}
              </span>
            </p>
          </div>
          <div
            v-if="auth.loading"
            class="ml-8 inline-flex h-9 w-20 items-center justify-center rounded-md bg-gray-200 shadow-sm animate-pulse"
          ></div>
          <RouterLink
            to="login"
            v-else-if="auth.user === null"
            class="ml-8 inline-flex items-center justify-center rounded-md border border-transparent bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700 hover:cursor-pointer"
          >
            Sign In
          </RouterLink>
          <button
            v-else
            @click="logout"
            class="ml-8 inline-flex items-center justify-center rounded-md border border-transparent bg-red-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-red-700 hover:cursor-pointer"
          >
            Logout
            <PulseLoader
              v-show="onlogout"
              class="ml-2"
              size="8px"
              color="white"
            />
          </button>
        </div>
      </div>
    </div>
  </header>
</template>

<style scoped></style>

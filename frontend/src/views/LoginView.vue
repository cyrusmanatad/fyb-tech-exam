<script setup>
import axios from "@/axios"; // axios.js file
import { useAuthStore } from "@/stores/auth";
import { reactive, ref } from "vue";
import { useRouter } from "vue-router";
import PulseLoader from 'vue-spinner/src/PulseLoader.vue'

const credentials = reactive({
  authMessage: "",
  email: {
    value: "",
    valid: true,
  },
  password: {
    value: "",
    valid: true,
  },
});

const router = useRouter();

const authenticating = ref(false);

const handleLogin = async () => {
  const auth = useAuthStore();

  authenticating.value = !authenticating.value;

  try {
    const response = await auth.login({
      email: credentials.email.value,
      password: credentials.password.value,
    });

    if (!response) {
      throw new Error("Invalid login response");
    }

    router.push({ name: "home" });
  } catch (error) {
    credentials.authMessage = "Invalid email or password.";

    credentials.email.valid = false;
    credentials.password.valid = false;
  } finally {
    authenticating.value = !authenticating.value;
  }
};
</script>

<template>
  <div
    class="flex h-[850px] flex-col justify-center bg-gray-50 py-12 sm:px-6 lg:px-8"
  >
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
      <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
        Sign in to your account
      </h2>
      <p class="mt-2 text-center text-sm text-gray-600">
        Or
        <a
          href="/register"
          class="font-medium text-blue-600 hover:text-blue-500"
        >
          create a new account
        </a>
      </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
      <div class="bg-white px-4 py-8 shadow sm:rounded-lg sm:px-10">
        <form class="space-y-6" v-on:submit.prevent="handleLogin">
          <div>
            <label
              htmlFor="email"
              class="block text-sm font-medium text-gray-700"
            >
              Email address
            </label>
            <div class="mt-1">
              <input
                id="email"
                name="email"
                type="email"
                autoComplete="email"
                v-model="credentials.email.value"
                required
                class="block w-full appearance-none rounded-md border px-3 py-2 placeholder-gray-400 shadow-sm focus:outline-none sm:text-sm"
                :class="[
                  credentials.email.valid
                    ? 'border-gray-300 focus:border-blue-500 focus:ring-blue-500'
                    : 'border-2 border-red-400',
                ]"
              />
            </div>
          </div>

          <div>
            <label
              htmlFor="password"
              class="block text-sm font-medium text-gray-700"
            >
              Password
            </label>
            <div class="mt-1">
              <input
                id="password"
                name="password"
                type="password"
                autoComplete="current-password"
                v-model="credentials.password.value"
                required
                class="block w-full appearance-none rounded-md border px-3 py-2 placeholder-gray-400 shadow-sm focus:outline-none sm:text-sm"
                :class="[
                  credentials.email.valid
                    ? 'border-gray-300 focus:border-blue-500 focus:ring-blue-500'
                    : 'border-2 border-red-400',
                ]"
              />
            </div>
          </div>

          <label
            htmlFor="email"
            v-show="
              credentials.email.valid === false ||
              credentials.password.valid === false
            "
            class="block text-sm font-medium text-red-700"
          >
            {{ credentials.authMessage }}
          </label>

          <div>
            <button
              type="submit"
              :disabled="authenticating"
              class="flex w-full cursor-pointer justify-center rounded-md border border-transparent bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:outline-none disabled:cursor-default disabled:bg-gray-400"
            >
              {{authenticating ? "Please wait" : "Sign In"}}
              <PulseLoader v-show="authenticating" class="ml-2" size="8px" color="white" />
            </button>
          </div>
        </form>

      </div>
    </div>
  </div>
</template>

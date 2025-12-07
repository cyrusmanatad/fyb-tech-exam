<script setup>
import axios from "@/axios"; // axios.js file
import { useAuthStore } from "@/stores/auth";
import { reactive, ref } from "vue";
import { useRouter } from "vue-router";
import PulseLoader from 'vue-spinner/src/PulseLoader.vue'

const credentials = reactive({
  authMessage: "",
  name: {
    value: "",
    valid: true,
  },
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

const registering = ref(false);

const handleRegister = async () => {
  const auth = useAuthStore();

  registering.value = !registering.value;

  try {
    const { data } = await axios.post("/api/auth/register", {
      name: credentials.name.value,
      email: credentials.email.value,
      password: credentials.password.value,
    });

    localStorage.setItem("auth_token", data.authorization.access_token);
    axios.defaults.headers.common["Authorization"] = `Bearer ${data.authorization.access_token}`;

    auth.user = data.user;

    router.push({ name: "home" });
  } catch (error) {
    credentials.authMessage = error.response.data.message;

    credentials.email.valid = false;
    credentials.password.valid = false;
  } finally {
    registering.value = !registering.value;
  }
};
</script>

<template>
  <div
    class="h-[850px] bg-gray-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8"
  >
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
      <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
        Create your account
      </h2>
      <p class="mt-2 text-center text-sm text-gray-600">
        Or 
        <a
          href="/login"
          class="font-medium text-blue-600 hover:text-blue-500"
        >
          sign in to your existing account
        </a>
      </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
      <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
        <form class="space-y-6" action="#" method="POST" v-on:submit.prevent="handleRegister">
          <div>
            <label
              htmlFor="name"
              class="block text-sm font-medium text-gray-700"
            >
              Full Name
            </label>
            <div class="mt-1">
              <input
                id="name"
                name="name"
                type="text"
                autoComplete="name"
                v-model="credentials.name.value"
                required
                class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
              />
            </div>
          </div>

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
                class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
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
                autoComplete="new-password"
                v-model="credentials.password.value"
                required
                class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
              />
            </div>
          </div>

          <div>
            <label
              htmlFor="confirm-password"
              class="block text-sm font-medium text-gray-700"
            >
              Confirm Password
            </label>
            <div class="mt-1">
              <input
                id="confirm-password"
                name="confirm-password"
                type="password"
                autoComplete="new-password"
                required
                class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
              />
            </div>
          </div>

          <div>
            <button
              type="submit"
              class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
            >
              {{registering ? "Please wait" : "Create Account"}}
              <PulseLoader v-show="registering" class="ml-2" size="8px" color="white" />
            </button>
          </div>
        </form>

      </div>
    </div>
  </div>
</template>

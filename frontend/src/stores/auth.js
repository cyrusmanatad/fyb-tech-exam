// src/stores/auth.js
import { defineStore } from "pinia";
import axios from "@/axios"; // axios.js file

export const useAuthStore = defineStore("auth", {
  state: () => ({
    user: null,
    accessToken: null,
    lastActivity: Date.now(),
    loading: false,
    status: null,
  }),
  actions: {
    async login(credentials) {
      try {
        const { data } = await axios.post("/api/auth/login", credentials);  
        
        this.accessToken = data.authorization.access_token;

        localStorage.setItem("auth_token", this.accessToken);

        axios.defaults.headers.common["Authorization"] = `Bearer ${this.accessToken}`;

        const { data: user, status } = await axios.get("/api/users/me");
        this.user = user.data;
        this.status = status;
        
        return true;
      } catch (error) {
        this.accessToken = null;
        return false;
      }
    },
    async fetchUser() {
      this.loading = true;
      try {
        const { data, status } = await axios.get("/api/users/me");
        this.user = data.data;
        this.status = status.status;
        
      } catch(error) {
        this.status = error.response.status
        this.user = null;
      } finally {
        this.loading = false;
      }
    },
    async refreshToken() {
      try {
        const { data } = await axios.post("/api/auth/refresh");
        this.accessToken = data.authorization.access_token;
        return true;
      } catch (error) {
        this.accessToken = null;
        return false;
      }
    },
    async logout() {
      await axios.post("/api/auth/logout");
      this.user = null;
      return true;
    },
  },
});

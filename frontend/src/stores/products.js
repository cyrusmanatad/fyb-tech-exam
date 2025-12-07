import { defineStore } from 'pinia'
import axios from "@/axios"; // axios.js file

export const useProductsStore = defineStore('products', {
  state: () => ({
    products: [],
    currentPage: 1,
    lastPage: 1,
    perPage: 5,
    from: 0,
    to: 0,
    total: 0,
    links: [],
    loading: false,
  }),
  actions: {
    async loadProducts(page = 1, search = '') {
      try {
        this.loading = true;
        let query = search ? `&search=${search}` : "";
        const { data } = await axios.get(`/api/products?page=${page}${query}`);
        
        this.products = data.data || [];
        this.currentPage = data.current_page || 1;
        this.lastPage = data.last_page || 1;
        this.perPage = data.per_page || 5;
        this.from = data.from || 0;
        this.to = data.to || 0;
        this.total = data.total || 0;
        this.links = data.links || [];
      } catch (error) {
        console.error("Failed to fetch products", error);
      } finally {
        this.loading = false;
      }
    },
    async deleteProduct(productId) {
      try {
        await axios.delete(`/api/products/${productId}`);
        this.products = this.products.filter(product => product.id !== productId);
        return true;
      } catch (error) {
        console.error("Failed to delete product", error);
        return false;
      } finally {
        this.loading = false;
      }
    }
  }
})

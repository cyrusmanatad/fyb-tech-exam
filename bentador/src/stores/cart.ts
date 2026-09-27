import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import type { Product } from '@/types/data-types'
import type { CartItem } from '@/types/order'

export const useCartStore = defineStore('cart', () => {
  const items = ref<CartItem[]>([])
  const isCartOpen = ref(false)

  const cartTotal = computed(() => {
    return items.value.reduce((total, item) => total + item.price * item.quantity, 0)
  })

  const cartCount = computed(() => {
    return items.value.reduce((total, item) => total + item.quantity, 0)
  })

  const addToCart = (product: Product) => {
    const existingItem = items.value.find((item) => item.base_sku === product.base_sku)
    if (existingItem) {
      existingItem.quantity++
    } else {
      // wip
      items.value.push({ ...product, quantity: 1, variant_id: 1, price_type: 'original' })
    }
  }

  const removeFromCart = (sku: string) => {
    items.value = items.value.filter((item) => item.base_sku !== sku)
  }

  const updateQuantity = (sku: string, delta: number) => {
    const item = items.value.find((item) => item.base_sku === sku)
    if (item) {
      item.quantity += delta
      if (item.quantity <= 0) {
        removeFromCart(sku)
      }
    }
  }

  const clearCart = () => {
    items.value = []
  }

  const toggleCart = (value?: boolean) => {
    isCartOpen.value = value !== undefined ? value : !isCartOpen.value
  }

  return {
    items,
    isCartOpen,
    cartTotal,
    cartCount,
    addToCart,
    removeFromCart,
    updateQuantity,
    clearCart,
    toggleCart,
  }
})

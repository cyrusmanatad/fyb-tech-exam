import { defineStore } from 'pinia'
import { ref, computed, watch } from 'vue'
import type { Product } from '@/types/data-types'
import type { CartItem } from '@/types/order'

const CART_STORAGE_KEY = 'bentadoor.cart.v1'

const asNumber = (value: unknown, fallback = Number.NaN): number => {
  const number = typeof value === 'number' ? value : Number(value)
  return Number.isFinite(number) ? number : fallback
}

const normalizeStoredCartItem = (value: unknown): CartItem | null => {
  if (!value || typeof value !== 'object') return null

  const item = value as CartItem
  const price = asNumber(item.price)
  const quantity = asNumber(item.quantity)

  if (
    typeof item.base_sku !== 'string' ||
    item.base_sku === '' ||
    typeof item.title !== 'string' ||
    !Number.isFinite(price) ||
    !Number.isFinite(quantity) ||
    quantity <= 0 ||
    (item.price_type !== 'sale' && item.price_type !== 'original')
  ) {
    return null
  }

  return {
    ...item,
    price,
    sale_price: asNumber(item.sale_price, 0),
    quantity,
  }
}

const readStoredCart = (): CartItem[] => {
  try {
    const raw = localStorage.getItem(CART_STORAGE_KEY)
    if (!raw) return []

    const parsed: unknown = JSON.parse(raw)
    if (!parsed || typeof parsed !== 'object') return []

    const document = parsed as { version?: unknown; items?: unknown }
    if (document.version !== 1 || !Array.isArray(document.items)) {
      localStorage.removeItem(CART_STORAGE_KEY)
      return []
    }

    return document.items.flatMap((item) => {
      const normalized = normalizeStoredCartItem(item)
      return normalized ? [normalized] : []
    })
  } catch {
    localStorage.removeItem(CART_STORAGE_KEY)
    return []
  }
}

const writeStoredCart = (items: CartItem[]) => {
  if (items.length === 0) {
    localStorage.removeItem(CART_STORAGE_KEY)
    return
  }

  localStorage.setItem(
    CART_STORAGE_KEY,
    JSON.stringify({
      version: 1,
      items,
    }),
  )
}

export const useCartStore = defineStore('cart', () => {
  const items = ref<CartItem[]>(readStoredCart())
  const isCartOpen = ref(false)

  watch(items, (value) => writeStoredCart(value), { deep: true, flush: 'sync' })

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
      items.value.push({
        ...product,
        price: asNumber(product.price, 0),
        sale_price: asNumber(product.sale_price, 0),
        quantity: 1,
        variant_id: 1,
        price_type: 'original',
      })
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

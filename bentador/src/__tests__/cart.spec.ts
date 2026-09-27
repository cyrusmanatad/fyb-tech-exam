import { describe, it, expect, beforeEach } from 'vitest'
import { createPinia, setActivePinia } from 'pinia'
import { useCartStore } from '@/stores/cart'
import type { Product } from '@/types/data-types'
import { ProductStatus, ProductStatusLabel } from '@/types/enum'

const product = {
  id: 1,
  base_sku: 'DOOR-1',
  title: 'Public Door',
  category_id: 1,
  category: 'Doors',
  currency: 'PHP',
  price: 1500,
  price_humanize: '1,500.00',
  sale_price: 1200,
  sp_humanize: '1,200.00',
  slug: 'public-door',
  uom: 'pcs',
  stock: 4,
  stock_humanize: '4',
  status: ProductStatus.PUBLISHED,
  status_label: ProductStatusLabel.PUBLISHED,
  createdAt: 'today',
  variants: [],
} satisfies Product

describe('cart store', () => {
  beforeEach(() => {
    localStorage.clear()
    setActivePinia(createPinia())
  })

  it('keeps items in localStorage and restores them in a new store', () => {
    const cart = useCartStore()

    cart.addToCart(product)
    cart.updateQuantity('DOOR-1', 1)

    const raw = localStorage.getItem('bentadoor.cart.v1')
    expect(raw).toBeTruthy()

    setActivePinia(createPinia())
    const restored = useCartStore()

    expect(restored.items).toHaveLength(1)
    expect(restored.items[0]?.quantity).toBe(2)
    expect(restored.items[0]?.title).toBe('Public Door')
    expect(restored.isCartOpen).toBe(false)
  })

  it('drops a cart document from an unknown version', () => {
    localStorage.setItem('bentadoor.cart.v1', JSON.stringify({ version: 2, items: [] }))

    const cart = useCartStore()

    expect(cart.items).toEqual([])
    expect(localStorage.getItem('bentadoor.cart.v1')).toBeNull()
  })

  it('removes the stored cart after it is cleared', () => {
    const cart = useCartStore()
    cart.addToCart(product)
    cart.clearCart()

    expect(localStorage.getItem('bentadoor.cart.v1')).toBeNull()
  })
})

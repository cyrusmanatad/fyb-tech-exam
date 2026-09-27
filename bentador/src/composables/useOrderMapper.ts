import type { CartItem, OrderPayload, OrderItem } from '@/types/order'

export function useOrderMapper() {
  const mapCartToPayload = (
    cartItems: CartItem[],
    options?: {
      currency?: string
      discount?: number
      tax?: number
      shipping_fee?: number
      payment_method?: string
      shipping_method?: string
      notes?: string
    },
  ): OrderPayload => {
    const items: OrderItem[] = cartItems.map((cartItem) => {
      // Find the selected variant
      const selectedVariant = cartItem.variants.find((v) => v.sku === cartItem.base_sku)

      // Determine price type — sale if sale_price differs from price
      const isSalePrice = selectedVariant && selectedVariant.sale_price < selectedVariant.price

      return {
        variant_id: selectedVariant?.id ?? cartItem.variant_id,
        quantity: cartItem.quantity,
        price_type: isSalePrice ? 'sale' : 'original',
      }
    })

    return {
      currency: options?.currency ?? 'PHP',
      discount: options?.discount ?? 0,
      tax: options?.tax ?? 0,
      shipping_fee: options?.shipping_fee ?? 0,
      payment_method: options?.payment_method ?? undefined,
      shipping_method: options?.shipping_method ?? undefined,
      notes: options?.notes ?? undefined,
      items,
    }
  }

  return { mapCartToPayload }
}

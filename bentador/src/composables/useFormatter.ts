export function useFormatter() {
  const formatNumber = (value: number | string, decimals = 0): string => {
    const num = typeof value === 'string' ? parseFloat(value) : value
    if (isNaN(num)) return '0'
    return new Intl.NumberFormat('en-US', {
      minimumFractionDigits: decimals,
      maximumFractionDigits: decimals,
    }).format(num)
  }

  const formatCurrency = (value: number | string, currency = 'USD'): string => {
    const num = typeof value === 'string' ? parseFloat(value) : value
    if (isNaN(num)) return '0.00'
    return new Intl.NumberFormat('en-US', {
      style: 'currency',
      currency: currency,
      minimumFractionDigits: 2,
      maximumFractionDigits: 2,
    }).format(num)
  }

  const formatStock = (value: number | string): string => {
    return formatNumber(value, 0) // no decimals for stock
  }

  return { formatNumber, formatCurrency, formatStock }
}

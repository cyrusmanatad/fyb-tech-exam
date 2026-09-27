import { ref } from 'vue'

export function useDownload() {
  const downloading = ref(false)

  const downloadPdf = (endpoint: string) => {
    downloading.value = true

    const token = localStorage.getItem('auth_token') ?? ''
    const url = `http://localhost:8000/${endpoint}?token=${token}`

    window.open(url, '_blank')

    // Give UI feedback briefly
    setTimeout(() => (downloading.value = false), 1500)
  }

  return { downloading, downloadPdf }
}

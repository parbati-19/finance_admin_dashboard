import { reactive } from 'vue'

type Toast = { id: number; message: string; type?: 'success' | 'error' }

const toasts = reactive<Toast[]>([])

export function useToast() {
  function show(message: string, type: Toast['type'] = 'success') {
    const id = Date.now() + Math.floor(Math.random() * 1000)
    toasts.push({ id, message, type })
    setTimeout(() => remove(id), 4000)
  }

  function remove(id: number) {
    const idx = toasts.findIndex((t) => t.id === id)

    if (idx !== -1){
        toasts.splice(idx, 1)
    }
  }

  return { toasts, show, remove }
}

export default useToast

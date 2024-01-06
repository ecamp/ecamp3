import { ref, onMounted, nextTick } from 'vue'

export function useDisplaySize() {
  const isPaperDisplaySize = ref(
    localStorage.getItem('activityIsPaperDisplaySize') !== 'false'
  )

  const isLocalPaperDisplaySize = ref(isPaperDisplaySize.value)

  onMounted(() => {
    isPaperDisplaySize.value =
      localStorage.getItem('activityIsPaperDisplaySize') !== 'false'
    isLocalPaperDisplaySize.value = isPaperDisplaySize.value
  })

  function toggleDisplaySize() {
    isPaperDisplaySize.value = !isPaperDisplaySize.value
    nextTick(() => {
      isLocalPaperDisplaySize.value = isPaperDisplaySize.value
    })
    localStorage.setItem('activityIsPaperDisplaySize', `${isPaperDisplaySize.value}`)
  }

  return {
    isPaperDisplaySize,
    isLocalPaperDisplaySize,
    toggleDisplaySize,
  }
}

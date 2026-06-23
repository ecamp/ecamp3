import { expect, afterEach, beforeEach } from 'vitest'
import { cleanup } from '@testing-library/vue'
import '@testing-library/jest-dom/vitest'
import snapshotSerializer from 'jest-serializer-vue-tjw'
import 'vitest-canvas-mock'
import { createLocalStorageFake } from '@/test/localStorageFake.js'

// jsdom does not implement elementFromPoint, but tiptap's placeholder extension
// calls it via posAtCoords in viewport tracking added in tiptap 3.24.0
if (!document.elementFromPoint) {
  document.elementFromPoint = () => null
}

// for all the imports
window.localStorage = createLocalStorageFake()
beforeEach(() => {
  window.localStorage = createLocalStorageFake()
})

// runs a cleanup after each test case (e.g. clearing jsdom)
afterEach(() => {
  cleanup()
})

expect.addSnapshotSerializer(snapshotSerializer)

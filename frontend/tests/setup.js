import { expect, afterEach } from 'vitest'
import { cleanup } from '@testing-library/vue'
import '@testing-library/jest-dom/vitest'
import snapshotSerializer from 'jest-serializer-vue-tjw'
import 'vitest-canvas-mock'

// jsdom does not implement elementFromPoint, but tiptap's placeholder extension
// calls it via posAtCoords in viewport tracking added in tiptap 3.24.0
if (!document.elementFromPoint) {
  document.elementFromPoint = () => null
}

// runs a cleanup after each test case (e.g. clearing jsdom)
afterEach(() => {
  cleanup()
})

expect.addSnapshotSerializer(snapshotSerializer)

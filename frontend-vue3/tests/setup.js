import { expect, afterEach } from 'vitest'
import { cleanup } from '@testing-library/vue'
import '@testing-library/jest-dom/vitest'
import snapshotSerializer from 'jest-serializer-vue-tjw'
import 'vitest-canvas-mock'

// runs a cleanup after each test case (e.g. clearing jsdom)
afterEach(() => {
  cleanup()
})

expect.addSnapshotSerializer(snapshotSerializer)

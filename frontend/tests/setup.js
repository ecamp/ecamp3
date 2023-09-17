import { expect, afterEach } from 'vitest'
import { cleanup } from '@testing-library/vue'
import matchers from '@testing-library/jest-dom/matchers'
import snapshotSerializer from 'jest-serializer-vue-tjw'
import 'vitest-canvas-mock'

// extends Vitest's expect method with methods from vue-testing-library
expect.extend(matchers)

// runs a cleanup after each test case (e.g. clearing jsdom)
afterEach(() => {
  cleanup()
})

expect.addSnapshotSerializer(snapshotSerializer)

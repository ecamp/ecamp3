import { expect, afterEach } from 'vitest'
import { cleanup } from '@testing-library/vue'
import '@testing-library/jest-dom/vitest'
import * as vueSnapshotSerializer from 'vue3-snapshot-serializer'
import 'vitest-canvas-mock'

// jsdom does not implement elementFromPoint, but tiptap's placeholder extension
// calls it via posAtCoords in viewport tracking added in tiptap 3.24.0
if (!document.elementFromPoint) {
  document.elementFromPoint = () => null
}

// jsdom does not implement ResizeObserver, which vuetify components such as
// VProgressCircular construct during setup
if (!global.ResizeObserver) {
  global.ResizeObserver = class {
    observe() {}
    unobserve() {}
    disconnect() {}
  }
}

// jsdom does not implement matchMedia, which components query for prefers-reduced-motion
if (!window.matchMedia) {
  window.matchMedia = (media) => ({
    media,
    matches: false,
    onchange: null,
    addEventListener() {},
    removeEventListener() {},
    addListener() {},
    removeListener() {},
    dispatchEvent: () => false,
  })
}

// runs a cleanup after each test case (e.g. clearing jsdom)
afterEach(() => {
  cleanup()
})

globalThis.vueSnapshots = {
  attributesToClear: ['id', 'for'],
  formatter: 'classic',
  classicFormatting: {
    indent_char: ' ',
    indent_inner_html: true,
    indent_size: 5,
    inline: [],
    sep: '\n',
    unformatted: [],
    wrap_attributes: 'force-aligned',
  },
}

expect.addSnapshotSerializer(vueSnapshotSerializer)

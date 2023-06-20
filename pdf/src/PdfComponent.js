import { styleStore } from './renderer/styleStore.js'

export default {
  props: {
    id: { type: String, default: '' },
  },
  beforeCreate() {
    Object.entries(this.$options?.pdfStyle || []).forEach(([selector, rules]) => {
      styleStore[selector] = styleStore[selector] || {}
      Object.assign(styleStore[selector], rules)
    })
  },
}

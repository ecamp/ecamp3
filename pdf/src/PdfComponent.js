import { styleStore } from './renderer/styleStore.js'

export default {
  beforeCreate() {
    Object.entries(this.$options.pdfStyle).forEach(([selector, rules]) => {
      styleStore[selector] = styleStore[selector] || {}
      Object.assign(styleStore[selector], rules)
    })
  },
}

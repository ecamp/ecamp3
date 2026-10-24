import { styleStore } from './renderer/styleStore.js'
import camelCase from 'lodash-es/camelCase.js'

export default {
  name: 'PdfComponent',
  props: {
    id: { type: String, default: '' },
    index: { type: Number, default: -1 },
    totalContents: { type: Number, default: 0 },
    type: { type: String, default: '' },
  },
  async beforeCreate() {
    Object.entries(this.$options?.pdfStyle || []).forEach(([selector, rules]) => {
      styleStore[selector] = styleStore[selector] || {}
      Object.assign(styleStore[selector], rules)
    })

    if (this.index !== -1) {
      await this.$onProgress('assembleContent', {
        content: this.index + 1,
        totalContents: this.totalContents,
        type: this.$tc(`print.${camelCase(this.type)}.title`),
      })
    }
  },
}

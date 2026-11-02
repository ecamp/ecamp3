import Image from '@tiptap/extension-image'
import { campImagePath } from '@/common/helpers/campImagePath.js'

export default Image.extend({
  addAttributes() {
    return {
      ...this.parent(),
      width: {
        default: null,
        parseHTML: (element) => {
          const width = Number(element.getAttribute('width'))
          return Number.isInteger(width) && width > 0 ? Math.min(width, 2400) : null
        },
      },
    }
  },
  parseHTML() {
    return [
      {
        tag: 'img[src]',
        getAttrs: (element) =>
          campImagePath(element.getAttribute('src')) ? null : false,
      },
    ]
  },
}).configure({ inline: true, allowBase64: false })

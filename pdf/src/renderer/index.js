import FontStore from '@react-pdf/font'
import renderPDF from '@react-pdf/render'
import PDFDocument from '@react-pdf/pdfkit'
import layoutDocument from '@react-pdf/layout'
import dayjs from '@/common/helpers/dayjs.js'
import { nodeOps } from './nodeOps.js'
// eslint-disable-next-line vue/prefer-import-from-vue
import { createRenderer } from '@vue/runtime-core'

const fontStore = new FontStore()

const pdf = (root, props) => {
  // For react-pdf, we need an object which will describe the structure of our
  // pdf document. Our nodeOps will read the Vue component tree and convert it to
  // the react-pdf-specific data structure inside doc.
  // For Vue, we need a "root container" (normally the <div id="app"> DOM element).
  // Vue uses this to keep track of which running Vue app this is. Since we don't do
  // update operations, it should be fine to just pass the doc object.
  const container = {}

  const render = async (compress = true) => {
    const { createApp } = createRenderer(nodeOps)
    const app = createApp(root, props)
    app.use(
      {
        install(app, options) {
          app.config.globalProperties.api = options.store
          app.config.globalProperties.$tc = options.$tc
          app.config.globalProperties.$date = dayjs
        },
      },
      props
    )
    app.mount(container)

    const documentProps = container.doc.props || {}
    const { pdfVersion, language, pageLayout, pageMode } = documentProps

    const ctx = new PDFDocument({
      compress,
      pdfVersion,
      lang: language,
      displayTitle: true,
      autoFirstPage: false,
      pageLayout,
      pageMode,
    })

    const layout = await layoutDocument(container.doc, fontStore)

    return renderPDF(ctx, layout)
  }

  const toBlob = async () => {
    const chunks = []
    const instance = await render()

    return new Promise((resolve, reject) => {
      instance.on('data', (chunk) => {
        chunks.push(chunk instanceof Uint8Array ? chunk : new Uint8Array(chunk))
      })

      instance.on('end', () => {
        try {
          const blob = new Blob(chunks, { type: 'application/pdf' })
          resolve(blob)
        } catch (error) {
          reject(error)
        }
      })
    })
  }

  return {
    toBlob,
  }
}

const Font = fontStore

const StyleSheet = {
  create: (s) => s,
}

export { Font, StyleSheet, pdf, createRenderer }

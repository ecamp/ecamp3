import FontStore from '@react-pdf/font'
import renderPDF from '@react-pdf/render'
import PDFDocument from '@react-pdf/pdfkit'
import layoutDocument from '@react-pdf/layout'
import { nodeOpsFor } from './nodeOps.js'
// eslint-disable-next-line vue/prefer-import-from-vue
import { createRenderer } from '@vue/runtime-core'

const fontStore = new FontStore()

const pdf = (root) => {
  const document = {}

  const render = async (compress = true) => {
    const { createApp } = createRenderer(nodeOpsFor(document))
    // TODO is it okay to misuse the document as the rootContainer here?
    //   This ensures that each call to pdf() gets a separate vue app instance.
    createApp(root).mount(document)

    const props = document.props || {}
    const { pdfVersion, language, pageLayout, pageMode } = props

    const ctx = new PDFDocument({
      compress,
      pdfVersion,
      lang: language,
      displayTitle: true,
      autoFirstPage: false,
      pageLayout,
      pageMode,
    })

    // TODO remove this, temporary fix as long as nodeOps aren't implemented
    document.children = []
    const layout = await layoutDocument(document, fontStore)

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

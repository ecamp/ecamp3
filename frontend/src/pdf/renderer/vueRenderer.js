import dayjs from '../../common/helpers/dayjs.js'
import { nodeOps } from './nodeOps.js'
import { createRenderer } from 'vue'

export function renderVueToPdfStructure(root, props = {}, onProgress = async () => {}) {
  // We need a "root container" (normally the <div id="app"> DOM element).
  // Vue uses this to keep track of which running Vue app this is.
  const container = {}

  const { createApp } = createRenderer(nodeOps)
  const app = createApp(root, props)
  app.use(
    {
      install(app, options) {
        app.config.globalProperties.api = options.store
        app.config.globalProperties.$tc = options.$tc
        app.config.globalProperties.$date = dayjs
        app.config.globalProperties.$toc = {}
        app.config.globalProperties.$onProgress = onProgress
      },
    },
    props
  )
  app.config.throwUnhandledErrorInProduction = true

  app.mount(container)

  return container.doc
}

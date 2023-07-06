import dayjs from '../../common/helpers/dayjs.js'
import { nodeOps } from './nodeOps.js'
// eslint-disable-next-line vue/prefer-import-from-vue
import { createRenderer } from '@vue/runtime-core'

export function renderVueToPdfStructure(root, props) {
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
      },
    },
    props
  )
  app.mount(container)

  return container.doc
}

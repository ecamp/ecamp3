import { pdf } from './renderer/index.js'
import ExampleDocument, { prepare } from './ExampleDocument.vue'

const render = (props = {}) => pdf(ExampleDocument, props)
render.prepare = prepare
export default render

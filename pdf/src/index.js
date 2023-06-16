import { pdf } from './renderer/index.js'
import ExampleDocument from './ExampleDocument.vue'

export default (props = {}) => pdf(ExampleDocument, props)
export { prepare } from './ExampleDocument.vue'

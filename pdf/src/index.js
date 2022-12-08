import { pdf } from './renderer/index.js'
import Doc from './ExampleDocument.vue'

export default () => pdf(Doc).toBlob()

import { pdf } from './renderer/index.js'
import CampPrint, { prepare } from './CampPrint.vue'

const render = (props = {}, onProgress) => pdf(CampPrint, props, onProgress)
export { render, prepare }

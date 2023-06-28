import { pdf } from './renderer/index.js'
import CampPrint, { prepare } from './CampPrint.vue'

const render = (props = {}) => pdf(CampPrint, props)
export { render, prepare }

import { createHalc } from 'halc'
import { hal } from 'halc/hal'
import { PlainObjectStore } from 'halc/plain'

export function createPrintApi(records) {
  return createHalc({ adapter: hal, store: new PlainObjectStore(records) })
}

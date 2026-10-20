import { createHalc } from '@ecamp/halc'
import { hal } from '@ecamp/halc/hal'
import { PlainObjectStore } from '@ecamp/halc/plain'

export function createPrintApi(records) {
  return createHalc({ adapter: hal, store: new PlainObjectStore(records) })
}

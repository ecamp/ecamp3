import { createHalc } from '@ecamp/halc'
import { hal } from '@ecamp/halc/hal'
import { PlainObjectStore } from '@ecamp/halc/plain'

/**
 * Creates a read-only API client in the PDF worker, over the records of the API store that the
 * main thread passed in. Reading something that is not in the records throws.
 */
export function createPrintApi(records) {
  return createHalc({ adapter: hal, store: new PlainObjectStore(records) })
}

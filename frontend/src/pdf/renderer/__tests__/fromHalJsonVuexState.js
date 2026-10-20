import { hal } from '@ecamp/halc/hal'

/**
 * Converts the state of a hal-json-vuex store (the format of the JSON fixtures in this folder)
 * into the records of a halc store, so the fixtures could stay unchanged.
 */
export default function fromHalJsonVuexState(state) {
  const records = {}
  for (const [uri, entry] of Object.entries(state)) {
    if (entry._meta?.virtual) continue
    const data = {}
    for (const [key, value] of Object.entries(entry)) {
      if (key === '_meta') continue
      if (value?.virtual === true) {
        // Embedded lists were stored under a virtual key, halc keeps them in the owner.
        data[key] = { hrefs: state[value.href].items.map((item) => item.href) }
      } else if (key === 'items' && Array.isArray(value)) {
        data[key] = { hrefs: value.map((item) => item.href) }
      } else {
        data[key] = value
      }
    }
    const meta = { version: 1, status: entry._meta?.loading ? 'loading' : 'loaded' }
    if (!entry._meta?.loading) meta.loadedAt = 0
    const type = hal.typeOf(uri)
    if (type) meta.type = type
    records[uri] = { uri, data, meta }
  }
  return records
}

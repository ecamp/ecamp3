import { hasQueryParam, removeQueryParam } from '@/store/uriUtils'

const PAGE_QUERY_PARAM = 'page'

export function isSinglePage (data) {
  return hasQueryParam(data._meta.self, PAGE_QUERY_PARAM)
}

export function getFullList (data, storeHas, api) {
  const uri = removeQueryParam(data._meta.self, PAGE_QUERY_PARAM)
  if (storeHas(uri)) {
    return api(uri)
  }
  return Collection.fromPage(uri, data, api)
}

export default class Collection {
  static fromArray (array) {
    return new ArrayCollection(array)
  }

  static fromPage (uri, page, api, onLoadedItem) {
    return new PaginatedCollection(uri, page, api, onLoadedItem)
  }

  constructor (items, loadingIterator, onLoadedItem) {
    this.items = items
    this._meta = { loadingIterator, onLoadedItem }
  }

  async load (numElementsToLoad = 1) {
    for (let i = 0; i < numElementsToLoad; i++) {
      let { done, value: item } = await this._meta.loadingIterator.next()
      if (done) break
      this._meta.onLoadedItem(item)
    }
  }
}

class ArrayCollection extends Collection {
  constructor (array) {
    super(array, { next: () => ({ done: true }) }, () => {})
  }
}

class PaginatedCollection extends Collection {
  constructor (uri, page, api, onLoadedItem) {
    const items = []
    super(items, paginatedIterator(page), onLoadedItem)
    this.copyProperties(page, ['prev', 'next'])
    this._meta.self = uri
  }

  async * [Symbol.asyncIterator] () {
    return paginatedIterator(this.first())
  }

  copyProperties (source, excludeKeys = []) {
    Object.keys(source).forEach(key => {
      if (!this.hasOwnProperty(key) && !excludeKeys.includes(key)) {
        this[key] = source[key]
      }
    })
  }
}

async function * paginatedIterator (page) {
  for (const item of page.items()) {
    yield item
  }
  if (page.next !== undefined) {
    let remainingPages = await page.next().loaded
    yield * paginatedIterator(remainingPages)
  }
}

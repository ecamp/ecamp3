import { removeQueryParam } from '@/store/uriUtils'

export const PAGE_QUERY_PARAM = 'page'

export function getFullList (vm, data) {
  const uri = removeQueryParam(data._links.self.href, PAGE_QUERY_PARAM)
  if (!vm.$store.state.api[uri]._meta.loading) {
    return vm.api(uri)
  }
  let page = { ...data }
  page._meta.self = uri
  return Collection.fromPage(page, vm.api)
}

export default class Collection {
  static fromArray (array) {
    return new ArrayCollection(array)
  }

  static fromPage (page, api, onLoadedItem) {
    return new PaginatedCollection(page, api, onLoadedItem)
  }

  constructor (items, loadingIterator, onLoadedItem) {
    this.items = items
    this._loadingIterator = loadingIterator
    this._onLoadedItem = onLoadedItem
  }

  async load (numElementsToLoad = 1) {
    for (let i = 0; i < numElementsToLoad; i++) {
      let { done, value: item } = await this._loadingIterator.next()
      if (done) break
      this._onLoadedItem(item)
    }
  }
}

class ArrayCollection extends Collection {
  constructor (array) {
    super(array, { next: () => ({ done: true }) }, () => {})
  }
}

class PaginatedCollection extends Collection {
  constructor (page, api, onLoadedItem) {
    const items = []
    super(items, paginatedIterator(page.items, page.next), onLoadedItem)
    copyProperties(page, { target: this }, ['items', 'next', 'prev'])
    this._meta.self = removeQueryParam(page._meta.self, PAGE_QUERY_PARAM)
  }

  async * [Symbol.asyncIterator] () {
    return paginatedIterator(this._page.items, this._page.next)
  }
}

function copyProperties (source, { target }, excludeKeys = []) {
  Object.keys(source).forEach(key => {
    if (!excludeKeys.includes(key)) {
      target[key] = source[key]
    }
  })
}

async function * paginatedIterator (page) {
  for (const item of page.items) {
    yield item
  }
  if (page.next !== undefined) {
    let remainingPages = await page.next().loaded
    yield * paginatedIterator(remainingPages)
  }
}

import Vue from 'vue'

export default class Collection {
  static fromArray (array) {
    return new ArrayBackedCollection(array)
  }

  static fromPage (page, onLoadedItem) {
    return new PaginatedCollection(page, onLoadedItem)
  }

  constructor (items, loadingIterator = null, onLoadedItem = () => {}) {
    this.items = items
    this._loadingIterator = loadingIterator
    this._onLoadedItem = onLoadedItem
  }

  async load (numElementsToLoad = 1) {
    if (!this._loadingIterator) return
    for (let i = 0; i < numElementsToLoad; i++) {
      let { done, value: item } = await this._loadingIterator.next()
      if (done) break
      this._onLoadedItem(item)
    }
  }
}

class ArrayBackedCollection extends Collection {}

class PaginatedCollection extends Collection {
  constructor (page, onLoadedItem) {
    const items = []
    super(items, paginatedIterator(page.items, page.next), onLoadedItem)
    this._page = page
    this.loading = true
    this.loaded = new Promise(async resolve => {
      await this.load(page._per_page)
      Vue.delete(this, 'loading')
      this.loaded = new Promise(resolve => resolve(this))
      resolve(this)
    })
    Object.keys(page).forEach(key => {
      if (!['items', 'next', 'prev', 'first', 'last'].includes(key)) {
        this[key] = page[key]
      }
    })
  }

  async * [Symbol.asyncIterator] () {
    return paginatedIterator(this._page.items, this._page.next)
  }
}

async function * paginatedIterator (initialItems, next = undefined) {
  for (const item of initialItems) {
    yield item
  }
  if (next !== undefined) {
    let remainingPages = await next().loaded
    yield * remainingPages
  }
}

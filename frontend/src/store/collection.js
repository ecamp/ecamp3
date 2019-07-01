import Vue from 'vue'

export default class Collection {
  static fromArray (array) {
    return new ArrayBackedCollection(array)
  }

  static fromPage (vm, page) {
    return new PaginatedCollection(vm, page)
  }

  constructor (items, loadingIterator = null) {
    this.items = items
    this._fullyLoaded = (loadingIterator === null)
    this._loadingIterator = loadingIterator
  }

  async load (numElementsToLoad = 1) {
    for (let i = 0; i < numElementsToLoad && !this._fullyLoaded; i++) {
      let next = await this._loadingIterator.next()
      this._fullyLoaded = next.done
    }
  }
}

class ArrayBackedCollection extends Collection {
  constructor (array) {
    super(array, arrayIterator(array), true)
  }
}

class PaginatedCollection extends Collection {
  constructor (vm, page) {
    const items = []
    const _appendItemToStore = (item, index) => appendItemToStore(vm, page.self, item, index)
    super(items, paginatedStoringIterator(page, items, _appendItemToStore))
    this._page = page
    this._appendItemToStore = _appendItemToStore
    this.loading = true
    this.loaded = new Promise(async resolve => {
      await this.load(page._per_page)
      Vue.delete(this, 'loading')
      this.loaded = new Promise(resolve => resolve(this))
      resolve(this)
    })
    Object.keys(page).forEach(key => {
      if (!(key in ['next', 'prev', 'first', 'last'])) {
        this[key] = page[key]
      }
    })
  }

  async * [Symbol.asyncIterator] () {
    return paginatedStoringIterator(this._page, this.items, this._appendItemToStore)
  }
}

function appendItemToStore (vm, collectionUri, item, index) {
  if (index >= vm.$store.state.api[collectionUri].items.length) {
    vm.$store.commit('appendCollectionItem', { item, collectionUri })
  }
}

function * arrayIterator (array) {
  for (const item of array) {
    yield item
  }
}

async function * paginatedIterator (initialItems, next = undefined) {
  yield * arrayIterator(initialItems)
  if (next !== undefined) {
    let remainingPages = await next().loaded
    yield * remainingPages
  }
}

async function * paginatedStoringIterator (page, numStoredItems, storeItem) {
  let index = 0
  for await (const item of paginatedIterator(page.items, page.next)) {
    storeItem(item, index)
    index++
    yield item
  }
}

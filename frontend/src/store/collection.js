export default class Collection {
  static fromArray (array) {
    return new ArrayCollection(array)
  }

  static fromPage (page, onLoadedItem) {
    return new PaginatedCollection(page, onLoadedItem)
  }

  constructor (items, loadingIterator, onLoadedItem) {
    this.items = items
    this._meta = { loadingIterator, onLoadedItem }
  }

  load (numElementsToLoad = 0) {
    return new Promise(async resolve => {
      const loadAll = numElementsToLoad === 0
      // eslint-disable-next-line no-unmodified-loop-condition
      for (let i = 0; i < numElementsToLoad || loadAll; i++) {
        let { done, value: item } = await this._meta.loadingIterator.next()
        if (done) break
        this._meta.onLoadedItem(item)
      }
      resolve(this)
    })
  }
}

class ArrayCollection extends Collection {
  constructor (array) {
    super(array, { next: () => ({ done: true }) }, () => {})
  }
}

class PaginatedCollection extends Collection {
  constructor (page, onLoadedItem) {
    const items = []
    super(items, paginatedIterator(page), onLoadedItem)
    this.copyProperties(page, ['prev', 'next', '_page'])
    this._meta = { ...page._meta, ...this._meta }
    // asynchronously load all items by default
    this.load()
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
  for (const item of page.items) {
    yield item
  }
  if (page.next !== undefined) {
    const next = page.next()
    const nextPage = await next._meta.loaded
    yield * paginatedIterator(nextPage)
  }
}

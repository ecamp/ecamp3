export default class Collection {
  static fromArray (array) {
    return new Collection(array)
  }

  static fromPage (page) {
    let collection = new Collection(async function * () {
      page = page.first()
      while (true) {
        for (let item of page.items) {
          yield item
        }
        if (!page.hasOwnProperty('next')) break
        page = page.next()
      }
    })
    collection.load(page.items.length)
    return collection
  }

  constructor (source) {
    if (Array.isArray(source)) {
      this.items = source
      this.fullyLoaded = true
      this.loadingIterator = null
    } else {
      this.items = []
      this.fullyLoaded = false
      this.loadingIterator = source()
    }
  }

  async load (numElementsToLoad = 1) {
    for (let i = 0; i < numElementsToLoad && !this.fullyLoaded; i++) {
      let next = this.loadingIterator.next()
      this.fullyLoaded = next.done
      this.items.push(next.value)
    }
  }
}

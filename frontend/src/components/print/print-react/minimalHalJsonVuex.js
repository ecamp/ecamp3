import { parseTemplate } from 'url-template'

function isEqualIgnoringOrder (array, other) {
  return array.length === other.length && array.every(elem => other.includes(elem))
}

function isCollection (object) {
  return !!(object && Array.isArray(object.items))
}

function isEntityReference (object) {
  if (!object) return false
  return isEqualIgnoringOrder(Object.keys(object), ['href'])
}

function isTemplatedLink (object) {
  if (!object) return false
  return isEqualIgnoringOrder(Object.keys(object), ['href', 'templated']) && (object.templated === true)
}

function normalizeEntityUri (uriOrEntity) {
  if (typeof uriOrEntity === 'function') {
    uriOrEntity = uriOrEntity()
  }
  if (typeof uriOrEntity === 'object') {
    uriOrEntity = uriOrEntity._meta?.self || ''
  }

  return uriOrEntity
}

const wrap = (storeData) => {
  const actions = {
    get: (uriOrEntity) => {
      const uri = normalizeEntityUri(uriOrEntity)

      const data = storeData[uri]
      const meta = data?._meta || { loading: true }

      if (meta.loading) {
        throw new Error(`The uri ${uri} is missing in the pre-loaded store data. Please make sure it is loaded before trying to access it using minimalHalJsonVuex`)
      }

      if (isCollection(data)) {
        return new Collection(data, actions)
      } else {
        return new StoreValue(data, actions)
      }
    },
    href: (uriOrEntity, relation) => {
      return actions.get(uriOrEntity).$href(relation)
    }
  }

  return actions
}

class StoreValue {
  constructor (storeData, actions) {
    this._storeData = storeData
    this._actions = actions

    Object.keys(storeData)
      .filter(key => key !== 'items') // exclude reserved properties
      .forEach(key => {
        const value = storeData[key]

        // storeData[key] is an embedded collection (need min. 1 item to detect an embedded collection)
        if (Array.isArray(value) && value.length > 0 && isEntityReference(value[0])) {
          this[key] = () => new EmbeddedCollection(value, actions)

        // storeData[key] is a reference only (contains only href; no data)
        } else if (isEntityReference(value)) {
          this[key] = () => actions.get(value.href)

        // storeData[key] is a templated link
        } else if (isTemplatedLink(value)) {
          this[key] = templateParams => actions.get(parseTemplate(value.href).expand(templateParams || {}))

        // storeData[key] is a primitive (normal entity property)
        } else {
          this[key] = value
        }
      })
  }

  $href (relation, templateParams = {}) {
    const rel = this._storeData[relation]
    if (rel?.templated) {
      return parseTemplate(rel?.href || '').expand(templateParams)
    }
    return rel?.href
  }
}

class Collection extends StoreValue {
  _filterDeleting (array) {
    return array.filter(entry => !entry._meta.deleting)
  }

  _replaceEntityReferences (array) {
    return array
      .filter(entry => isEntityReference(entry))
      .map(entry => this._actions.get(entry.href))
  }

  get items () {
    return this._filterDeleting(this._replaceEntityReferences(this._storeData.items))
  }

  get allItems () {
    return this._replaceEntityReferences(this._storeData.items)
  }
}

class EmbeddedCollection extends Collection {
  constructor (data, actions) {
    super({ items: data, _meta: { loading: false } }, actions)
  }
}

export default wrap

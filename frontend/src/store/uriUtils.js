
export const API_ROOT = process.env.VUE_APP_ROOT_API

export function hasQueryParam (uri, paramName) {
  let queryStart = uri.indexOf('?')
  if (queryStart === -1) return false
  let query = new URLSearchParams(uri.substring(queryStart + 1))
  return [...query.keys()].includes(paramName)
}

export function removeQueryParam (uri, paramName) {
  return modifyQueryParams(uri, (set, keys, query) => {
    for (const key of keys) {
      if (key !== paramName) {
        set(key, query.get(key))
      }
    }
  })
}

export function sortQueryParams (uri) {
  return modifyQueryParams(uri, (set, keys, params) => {
    for (const key of [ ...new Set(keys) ].sort()) {
      for (const value of params.getAll(key)) {
        set(key, value)
      }
    }
  })
}

function modifyQueryParams (uri, modifierFunction) {
  let queryStart = uri.indexOf('?')
  if (queryStart === -1) return uri
  let prefix = uri.substring(0, queryStart)
  let query = new URLSearchParams(uri.substring(queryStart + 1))
  let modifiedQuery = new URLSearchParams()
  modifierFunction((key, value) => modifiedQuery.append(key, value), query.keys(), query)
  if ([...modifiedQuery.keys()].length) {
    return prefix + '?' + modifiedQuery.toString()
  }
  return prefix
}

export function hasQueryParam (uri, paramName) {
  let queryStart = uri.indexOf('?')
  if (queryStart === -1) return false
  let query = new URLSearchParams(uri.substring(queryStart + 1))
  return [...query.keys()].includes(paramName)
}

export function sortQueryParams (uri) {
  let queryStart = uri.indexOf('?')
  if (queryStart === -1) return uri
  let prefix = uri.substring(0, queryStart)
  let query = new URLSearchParams(uri.substring(queryStart + 1))
  let modifiedQuery = new URLSearchParams()

  for (const key of [ ...new Set(query.keys()) ].sort()) {
    for (const value of query.getAll(key)) {
      modifiedQuery.append(key, value)
    }
  }

  if ([...modifiedQuery.keys()].length) {
    return prefix + '?' + modifiedQuery.toString()
  }
  return prefix
}

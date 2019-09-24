export function hasQueryParam (uri, paramName) {
  let queryStart = uri.indexOf('?')
  if (queryStart === -1) return false
  let query = new URLSearchParams(uri.substring(queryStart + 1))
  return [...query.keys()].includes(paramName)
}

function sortQueryParams (uri) {
  const queryStart = uri.indexOf('?')
  if (queryStart === -1) return uri
  const prefix = uri.substring(0, queryStart)
  const query = new URLSearchParams(uri.substring(queryStart + 1))
  const modifiedQuery = new URLSearchParams();

  [...new Set(query.keys())].sort().forEach((key) => {
    query.getAll(key).forEach((value) => {
      modifiedQuery.append(key, value)
    })
  })

  if ([...modifiedQuery.keys()].length) {
    return `${prefix}?${modifiedQuery.toString()}`
  }
  return prefix
}

export function normalizeUri (uri, baseUrl = '') {
  if (typeof uri !== 'string' || uri === '') return null
  return sortQueryParams(uri).replace(new RegExp(`^${baseUrl}`), '')
}

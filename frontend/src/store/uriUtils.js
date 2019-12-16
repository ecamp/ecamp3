/**
 * Sorts the query parameters in a URI, keeping the values of duplicate keys in order.
 * Example:
 * sortQueryParams('localhost/api/camps?q=something&dup=true&alpha=0&dup=false')
 * // 'localhost/api/camps?alpha=0&dup=true&dup=false&q=something'
 * @param uri      to be processed
 * @returns string URI with sorted query parameters
 */
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

/**
 * Extracts the URI from an entity (or uses the passed URI if it is a string) and normalizes it for use in
 * the Vuex store.
 * @param uriOrEntity     entity or literal URI string
 * @param baseUrl         common URI prefix to remove during normalization
 * @returns {null|string} normalized URI, or null if the uriOrEntity argument was not understood
 */
export function normalizeEntityUri (uriOrEntity, baseUrl = '') {
  if (uriOrEntity === undefined) return normalizeUri('', baseUrl)
  if (typeof uriOrEntity === 'string') return normalizeUri(uriOrEntity, baseUrl)
  return normalizeUri(((uriOrEntity || {})._meta || {}).self, baseUrl)
}

/**
 * Normalize a URI by sorting the query parameters and removing a given prefix.
 * @param uri             to be normalized
 * @param baseUrl         prefix to remove from the beginning of the URI if present
 * @returns {string|null} normalized URI, or null if uri is not a string
 */
function normalizeUri (uri, baseUrl) {
  if (typeof uri !== 'string') return null
  return sortQueryParams(uri).replace(new RegExp(`^${baseUrl}`), '')
}

/**
 * Allowed filter values that are not HalUris
 * @typedef {'none'} SpecialValues
 */
/**
 * The Type of Object in qualified hal format for api
 * @typedef {'categories'|'camp_collaborations'|'periods'|'activity_progress_labels'} HalType
 */
/**
 * Format of the Hal Uri
 * @typedef {`/${HalType}/${string}` | SpecialValues} HalUri
 */
/**
 * Formats the Hal-Object-URI to the id
 * @param uri {HalUri} the ID (from the hal object)
 * @return {string} the ID of the uri
 */
export const halUriToId = (uri) => {
  if (uri === 'none') return uri
  return uri.substring(uri.lastIndexOf('/') + 1)
}
/**
 * Formats an id and a Datatype to a hal-object-id
 * @param dataType {HalType} the datatype of the object
 * @param id {string} the id of the object
 * @return {HalUri}
 */
export const idToHalUri = (dataType, id) => {
  if (id === 'none') return id
  return `/${dataType}/${id}`
}

/**
 * Transforms an object with halUriValues to an object with corresponding hal IDs.
 * @param {Record<string, HalUri|HalUri[]|null|undefined>} uriObj - Object with halUriValues
 * @returns {Record<string, string|string[]>} - Object with hal IDs
 */
export const transformValuesToHalId = (uriObj) => {
  const transformedEntries = Object.entries(uriObj)
    .filter(([_, value]) => !!value)
    .filter(
      ([_, value]) =>
        typeof value === 'string' || (Array.isArray(value) && value.length !== 0)
    )
    .map(([key, value]) => [
      key,
      Array.isArray(value) ? value.map(halUriToId) : halUriToId(value),
    ])
  return Object.fromEntries(transformedEntries)
}

/**
 * Transforms a query parameter based on its type and value.
 * @internal
 * @param {UrlParamKey} key - The query parameter key.
 * @param {string|string[]} value - The query parameter value.
 * @param {HalType} type - The datatype of the object.
 * @returns {[string, HalUri|HalUri[]|null]} - The transformed query parameter entry.
 */
export function transformQueryParam(key, value, type) {
  if (typeof value === 'string') {
    const halUriValue =
      key === 'period' ? idToHalUri(type, value) : [idToHalUri(type, value)]
    return [key, halUriValue]
  } else if (Array.isArray(value)) {
    const uriValues = value
      .filter((entry) => !!entry)
      .map((entry) => idToHalUri(type, entry))
    return [key, uriValues]
  } else {
    return [key, null]
  }
}

/**
 * @typedef {Record<string, HalUri[]|(HalUri|null)>} ActivityFilter
 */
/**
 * Allowed Url param keys
 * @typedef {'period'|'responsible'|'category'|'progressLabels'} UrlParamKey
 */
/**
 * The Allowed Url parameter keys
 * @type {UrlParamKey[]} UrlParamKeys
 * @readonly
 */
const urlParamKeys = ['period', 'responsible', 'category', 'progressLabel']
/**
 * Map for url param keys to hal types
 * @type {Record<UrlParamKey,HalType >}
 */
const URL_PARAM_TO_HAL_TYPE = {
  category: 'categories',
  responsible: 'camp_collaborations',
  period: 'periods',
  progressLabel: 'activity_progress_labels',
}

/**
 * Processes the route query and returns a filtered and transformed object.
 * @param {Dictionary<string | (string | null)[]>} query - The route query object.
 * @returns {Record<UrlParamKey, HalUri|HalUri[]>} - The processed filter object.
 */
export function processRouteQuery(query) {
  return Object.fromEntries(
    Object.entries(query)
      .filter((value) => isValidParamEntry(value))
      .map(([key, value]) => [key, value, URL_PARAM_TO_HAL_TYPE[key]])
      .map(([key, value, type]) => transformQueryParam(key, value, type))
      .filter(([_, value]) => !!value)
  )
}

/**
 * @param {[string, (string | null)[]]} entry
 * @returns {entry is ({([UrlParamKey,string|string[]])})}
 */
function isValidParamEntry(entry) {
  const [key, value] = entry
  const keyIsValid = urlParamKeys.includes(key)
  const valueIsValid = Array.isArray(value) ? value.length !== 0 : !!value
  return keyIsValid && valueIsValid
}

/**
 * Checks if the filter contains updated values
 * @param {Record<string, string|string[]>} filter
 * @param {Dictionary<string | (string | null)[]>} query
 * @return {boolean}
 */
export function filterAndQueryAreEqual(filter, query) {
  if (JSON.stringify(filter) === JSON.stringify(query)) return true
  const arrayFiltersAreEqual = ['category', 'responsible', 'progressLabel']
    .map((key) => ({
      a: getValueAsArrayForKey(query, key),
      b: getValueAsArrayForKey(filter, key),
    }))
    .map(({ a, b }) => JSON.stringify(a) === JSON.stringify(b))
    .reduce((accum, curr) => accum && curr, true)
  return arrayFiltersAreEqual && filter.period === query.period
}

/**
 * @internal
 * @template T
 * @param {T} obj
 * @param {keyof T} key
 * @return {string[] | undefined}
 */
function getValueAsArrayForKey(obj, key) {
  const val = obj[key]
  if (Array.isArray(val)) return val.filter((v) => !!v && typeof v === 'string').sort()
  if (typeof val === 'string') return [val]
  return undefined
}

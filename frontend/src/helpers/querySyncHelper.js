import { idToHalUri } from '@/helpers/formatHalHelper'

/**
 * Converts a query object to a query string.
 * @param {Record<string, string|string[]|null|undefined>} queryObj - The query object.
 * @returns {string} The query string.
 */
export function getQueryAsString(queryObj) {
  const queryParams = []

  for (const [key, values] of Object.entries(queryObj)) {
    if (values === null || values === undefined) continue
    const normalizedValues = Array.isArray(values) ? values : [values]

    const paramStrings = normalizedValues.map(
      (value) => `${encodeURIComponent(key)}=${encodeURIComponent(value)}`
    )

    queryParams.push(...paramStrings)
  }

  return '?' + queryParams.join('&')
}

/**
 * Transforms a query parameter based on its type and value.
 * @param {string} key - The query parameter key.
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
 * The Allowed Url parameter keys
 * @type {UrlParamKey[]} UrlParamKeys
 */
const urlParamKeys = ['period', 'responsible', 'category', 'progressLabel']
/**
 * Map for url param keys to hal types
 * @type {{(typeof urlParamKeys[number]):HalType }}
 */
const URL_PARAM_TO_HAL_TYPE = {
  category: 'categories',
  responsible: 'camp_collaborations',
  period: 'periods',
  progressLabel: 'activity_progress_labels',
}

/**
 * Processes the route query and returns a filtered and transformed object.
 * @param {Partial<ActivityFilter>} query - The route query object.
 * @returns {Record<string, HalUri|HalUri[]>} - The processed filter object.
 */
export function processRouteQuery(query) {
  return Object.fromEntries(
    Object.entries(query)
      .filter(([key, value]) => urlParamKeys.includes(key) && !!value)
      .map(([key, value]) => [key, value, URL_PARAM_TO_HAL_TYPE[key]])
      .map(([key, value, type]) => transformQueryParam(key, value, type))
      .filter(([_, value]) => !!value)
  )
}

/**
 * Load and process collections from the API.
 * @param {Object} camp - The camp object.
 * @returns {Promise<{
 *   categories: string[],
 *   periods: string[],
 *   collaborators: string[],
 *   progressLabels: string[]
 * }>} - A promise that resolves with the processed collections.
 */
export async function loadAndProcessCollections(camp) {
  const loadedCollections = await Promise.all([
    camp.categories()._meta.load,
    camp.periods()._meta.load,
    camp.campCollaborations()._meta.load,
    camp.progressLabels()._meta.load,
    camp.activities()._meta.load,
  ])

  const [categories, periods, collaborators, progressLabels] = loadedCollections
    .slice(0, 4)
    .map((collection) => collection.allItems.map((entry) => entry._meta.self))

  return {
    categories,
    periods,
    collaborators,
    progressLabels,
  }
}

/**
 * Allowed Url param keys
 * @typedef {'period'|'responsible'|'category'} UrlParamKey
 */
/**
 * @typedef ActivityFilter
 * @type {object}
 * @property { null | string} period
 * @property {string[]} responsible
 * @property {string[]} category
 * @property {string[]} progressLabel
 */

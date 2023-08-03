/**
 * The Type of Object in qualified hal format for api
 * @typedef {'categories'|'camp_collaborations'|'periods'|'activity_progress_labels'} HalType
 */
/**
 * Format of the Hal Uri
 * @typedef {`/${HalType}/${string}`} HalUri
 */
/**
 * Formats the Hal-Object-URI to the id
 * @param uri {HalUri} the ID (from the hal object)
 * @return {string} the ID of the uri
 */
export const halUriToId = (uri) => {
  return uri.substring(uri.lastIndexOf('/') + 1)
}
/**
 * Formats an id and a Datatype to a hal-object-id
 * @param dataType {HalType} the datatype of the object
 * @param id {string} the id of the object
 * @return {HalUri}
 */
export const idToHalUri = (dataType, id) => {
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
    .map(([key, value]) => [
      key,
      Array.isArray(value) ? value.map(halUriToId) : halUriToId(value),
    ])

  return Object.fromEntries(transformedEntries)
}

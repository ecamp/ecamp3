/**
 * The Type of Object in qualified hal format for api
 * @typedef {'categories'|'camp_collaborations'|'periods'} HalType
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

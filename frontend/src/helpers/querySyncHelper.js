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

  if (queryParams.length === 0) {
    return ''
  }

  return '?' + queryParams.join('&')
}

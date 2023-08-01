/**
 *
 * @param queryObj{{[p: string]: string[] | string}}
 * @return {string}
 */
export function getQueryAsString(queryObj) {
  const params = Object.entries(queryObj).map(([key, vals]) => {
    if (typeof vals === 'string') {
      return `&${key}=${vals}`
    } else {
      let q = ''
      for (const val of vals) {
        q = `${q}&${key}=${val}`
      }
      return q
    }
  })
  if (params.length === 0) return ''
  return '?' + params.join('').slice(1)
}

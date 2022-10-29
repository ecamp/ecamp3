/**
 * Parse Hex-Color;
 * @param color {string} Input as a hex string '#000000'
 * Returns an array with [r, g, b]-Values
 */
function parseHexColor(color) {
  const r = parseInt(color.substr(1, 2), 16)
  const g = parseInt(color.substr(3, 2), 16)
  const b = parseInt(color.substr(5, 2), 16)

  return [r, g, b]
}

/**
 * @see https://stackoverflow.com/a/64090995/2493809
 * @param h {number} Hue 0…360
 * @param s {number} Saturation 0…1
 * @param l {number} Lightness 0…1
 * @return {[r:number, g:number, b:number]} Returns an array with [r, g, b]-Values
 */
function convertHslColor(h, s, l) {
  let a = s * Math.min(l, 1 - l)
  let f = (n, k = (n + h / 30) % 12) => l - a * Math.max(Math.min(k - 3, 9 - k, 1), -1)
  return [f(0), f(8), f(4)]
}

/**
 * @param r {number} 0…1
 * @param g {number} 0…1
 * @param b {number} 0…1
 * @returns {string} color black or white depending on RGB-Background-Color
 */
function contrastColor(r, g, b) {
  const y =
    Math.pow(r / 255.0, 2.2) * 0.2126 +
    Math.pow(g / 255.0, 2.2) * 0.7152 +
    Math.pow(b / 255.0, 2.2) * 0.0722

  return y < 0.38 ? '#FFFFFF' : '#000000'
}

/**
 * @param r {number} 0…1
 * @param g {number} 0…1
 * @param b {number} 0…1
 * @param a {number} 0…1
 * @returns {string}
 */
function rgbToStringColor(r, g, b, a = 1) {
  return `rgb(${r * 255},${g * 255},${b * 255},${a})`
}

/**
 * @param h {number} 0…360
 * @param s {number} 0…1
 * @param l {number} 0…1
 * @param a {number?} 0…1
 * @returns {string}
 */
function hslToStringColor(h, s, l, a = 1) {
  return `hsl(${h},${s * 100}%,${l * 100}%,${a})`
}

/**
 * @param id {string} generated id
 * @param inactive {boolean} status
 * @returns {[h:number, s:number, l:number]} hsl color [0…360, 0…1, 0…1]
 */
function idToHslColor(id, inactive = false) {
  if (!id) {
    return [0, 0, 0.3]
  }
  const h = parseInt(id, 16) % 360 || 0
  const s = inactive ? 0 : 1
  return [h, s, 0.3]
}

/**
 * @returns {[h:number, s:number, l:number]} color for a user based on their id [0…360, 0…1, 0…1]
 */
function userHslColor(user) {
  return idToHslColor(user.id, user._meta?.loading)
}

/**
 * @returns {[h:number, s:number, l:number]} color for a camp collaboration based on its user id and status
 */
function campCollaborationHslColor(campCollaboration) {
  if (!campCollaboration) {
    return idToHslColor('', true)
  }

  const loading =
    campCollaboration._meta?.loading ||
    (typeof campCollaboration.user === 'function' &&
      campCollaboration.user()._meta?.loading)

  return idToHslColor(
    typeof campCollaboration.user === 'function'
      ? campCollaboration.user().id
      : campCollaboration.id,
    campCollaboration.status === 'inactive' || loading
  )
}

export {
  parseHexColor,
  convertHslColor,
  contrastColor,
  rgbToStringColor,
  hslToStringColor,
  userHslColor,
  campCollaborationHslColor,
  idToHslColor,
}

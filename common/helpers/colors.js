import {
  getColor,
  parse,
  serialize,
  contrast,
  HSL,
  sRGB,
  ColorSpace,
} from 'colorjs.io/fn'

ColorSpace.register(sRGB)
ColorSpace.register(HSL)

/**
 * @param color {string} CSS compatible string color
 * @returns {string} colorblack or white depending on input color
 */
function contrastColor(color) {
  const input = parse(color)
  const black = parse('#000')
  const white = parse('#fff')
  const blackContrast = contrast(input, black, 'APCA')
  const whiteContrast = contrast(input, white, 'APCA')
  return blackContrast > whiteContrast
    ? serialize(black, { format: 'hex' })
    : serialize(white, { format: 'hex' })
}

/**
 * @param id {string} generated id
 * @param inactive {boolean} status
 * @returns {string} hsl color
 */
function idToColor(id, inactive = false) {
  if (!id) {
    return serialize(getColor({ space: HSL, coords: [0, 0, 30] }), { format: 'hex' })
  }
  return serialize(
    getColor({
      space: HSL,
      coords: [parseInt(id, 16) % 360 || 0, inactive ? 0 : 100, 30],
    }),
    { format: 'hex' }
  )
}

/**
 * @returns {string}
 */
function defaultColor() {
  return serialize(getColor({ space: HSL, coords: [0, 0, 10] }), { format: 'hex' })
}

/**
 * @returns {string} color for a user based on their id
 */
function userColor(user) {
  return idToColor(user.id, user._meta?.loading)
}

/**
 * @returns {string} color for a camp collaboration based on its user id and status
 */
function campCollaborationColor(campCollaboration) {
  if (!campCollaboration) {
    return idToColor('', true)
  }

  const loading =
    campCollaboration._meta?.loading ||
    (typeof campCollaboration.user === 'function' &&
      campCollaboration.user()._meta?.loading)

  return idToColor(
    typeof campCollaboration.user === 'function'
      ? campCollaboration.user().id
      : campCollaboration.id,
    campCollaboration.status === 'inactive' || loading
  )
}

export { contrastColor, defaultColor, userColor, campCollaborationColor, idToColor }

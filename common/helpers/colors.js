import Color from 'colorjs.io'

/**
 * @param color {string} CSS compatible string color
 * @returns {string} colorblack or white depending on input color
 */
function contrastColor(color) {
  const input = new Color(color)
  const black = new Color('#000')
  const white = new Color('#fff')
  const blackContrast = Math.abs(input.contrast(black, 'APCA'))
  const whiteContrast = Math.abs(input.contrast(white, 'APCA'))
  return blackContrast > whiteContrast
    ? black.toString({ format: 'hex' })
    : white.toString({ format: 'hex' })
}

/**
 * @param id {string} generated id
 * @param inactive {boolean} status
 * @returns {string} hsl color
 */
function idToColor(id, inactive = false) {
  if (!id) {
    return new Color('HSL', [0, 0, 30]).to('srgb').toString({ format: 'hex' })
  }
  return new Color('HSL', [parseInt(id, 16) % 360 || 0, inactive ? 0 : 100, 30])
    .to('srgb')
    .toString({ format: 'hex' })
}

/**
 * @returns {string}
 */
function defaultColor() {
  return new Color('HSL', [0, 0, 10]).toString({
    format: 'hex',
  })
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

import Color from 'colorjs.io'

/**
 * @param color {Color}
 * @returns {Color} color black or white depending on RGB-Background-Color
 */
function contrastColor(color) {
  const black = new Color('#000')
  const white = new Color('#fff')
  const blackContrast = color.contrast(black, 'DeltaPhi')
  const whiteContrast = color.contrast(white, 'DeltaPhi')
  return blackContrast > whiteContrast ? black : white
}

/**
 * @param id {string} generated id
 * @param inactive {boolean} status
 * @returns {Color} hsl color [0…360, 0…1, 0…1]
 */
function idToColor(id, inactive = false) {
  if (!id) {
    return new Color('hsl', [0, 0, 30])
  }
  return new Color('hsl', [parseInt(id, 16) % 360 || 0, inactive ? 0 : 100, 30])
}

/**
 * @returns {Color} color for a user based on their id [0…360, 0…1, 0…1]
 */
function defaultColor() {
  return new Color('hsl', [0, 0, 10])
}

/**
 * @returns {Color} color for a user based on their id [0…360, 0…1, 0…1]
 */
function userColor(user) {
  return idToColor(user.id, user._meta?.loading)
}

/**
 * @returns {Color} color for a camp collaboration based on its user id and status
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

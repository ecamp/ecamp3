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
function userColor(user, inactive = user._meta?.loading) {
  if (user.color && !inactive) {
    return user.color
  }
  return idToColor(user.id, inactive)
}

/**
 * @returns {string} color for a camp collaboration based on its user id and status
 */
function campCollaborationColor(campCollaboration) {
  if (!campCollaboration) {
    return idToColor('', true)
  }

  const inactive =
    campCollaboration._meta?.loading || campCollaboration.status === 'inactive'

  if (campCollaboration?.color && !inactive) {
    return campCollaboration.color
  }

  if (typeof campCollaboration.user === 'function') {
    return userColor(
      campCollaboration.user(),
      inactive || campCollaboration.user()._meta?.loading
    )
  } else {
    return idToColor(campCollaboration.id, inactive)
  }
}

export { contrastColor, defaultColor, userColor, campCollaborationColor, idToColor }

/**
 * Parse Hex-Color;
 * Returns an array with [r, g, b]-Values
 */
function parseHexColor(color) {
  const r = parseInt(color.substr(1, 2), 16)
  const g = parseInt(color.substr(3, 2), 16)
  const b = parseInt(color.substr(5, 2), 16)

  return [r, g, b]
}

/**
 * Returns text color black or white depending on RGB-Background-Color
 */
function contrastColor(r, g, b) {
  const y =
    Math.pow(r / 255.0, 2.2) * 0.2126 +
    Math.pow(g / 255.0, 2.2) * 0.7152 +
    Math.pow(b / 255.0, 2.2) * 0.0722

  return y < 0.38 ? '#FFF' : '#000'
}

export { parseHexColor, contrastColor }

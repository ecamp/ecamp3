/**
 * Returns text color black or white depending on RGB-Background-Color
 */
export default function (r, g, b) {
  const y =
    Math.pow(r / 255.0, 2.2) * 0.2126 +
    Math.pow(g / 255.0, 2.2) * 0.7152 +
    Math.pow(b / 255.0, 2.2) * 0.0722

  return y < 0.38 ? '#FFF' : '#000'
}

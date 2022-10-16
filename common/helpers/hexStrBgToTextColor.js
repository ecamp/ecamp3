import backgroundToTextColor from './backgroundToTextColor.js'

export default function (color) {
  const r = parseInt(color.substr(1, 2), 16)
  const g = parseInt(color.substr(3, 2), 16)
  const b = parseInt(color.substr(5, 2), 16)

  return backgroundToTextColor(r, g, b)
}

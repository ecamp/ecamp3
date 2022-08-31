/**
 * Returns a color value based on an id and optionally a status
 */
export default function (id, inactive = false) {
  const h = parseInt(id, 16) % 360
  const l = inactive ? '0%' : '30%'
  return `hsl(${h},100%,${l})`
}

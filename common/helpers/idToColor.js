/**
 * Returns a color value based on an id and optionally a status
 */
export default function (id, inactive = false) {
  if (!id) {
    return 'hsl(0,0%,30%)'
  }
  const h = (parseInt(id, 16) % 360) || 0
  const s = inactive ? '0%' : '100%'
  return `hsl(${h},${s},30%)`
}

import runes from 'runes'

/**
 * Returns two characters to display for a display name
 */
export default function (displayName) {
  if (!displayName) return ''

  let items = displayName.split(' ', 2)
  if (items.length === 1) {
    items = items.shift().split(/[,._-]/, 2)
  }
  if (items.length === 1) {
    return runes.substr(displayName, 0, 2).toUpperCase()
  } else {
    return (
      runes.substr(items[0], 0, 1).toUpperCase() +
      runes.substr(items[1], 0, 1).toUpperCase()
    )
  }
}

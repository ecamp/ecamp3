/**
 * Returns two characters to display for a display name
 */
export default function (displayName) {
  let items = displayName.split(' ', 2)
  if (items.length === 1) {
    items = items.shift().split(/[,._-]/, 2)
  }
  if (items.length === 1) {
    return displayName.substr(0, 2).toUpperCase()
  } else {
    return items[0].substr(0, 1).toUpperCase() + items[1].substr(0, 1).toUpperCase()
  }
}

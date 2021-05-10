import cloneDeep from 'lodash/cloneDeep'

const max = function (slotNames) {
  return slotNames.reduce((max, slotName) => {
    if (slotName.length > max.length) return slotName
    if (slotName.length < max.length) return max
    return slotName > max ? slotName : max
  }, '')
}

const isIncrementable = function (character) {
  return (character >= '0' && character < '9') ||
    (character >= 'a' && character < 'z') ||
    (character >= 'A' && character < 'Z')
}

const next = function (slotName) {
  if (slotName === '') return '1'

  let i = slotName.length - 1
  while (!isIncrementable(slotName[i]) && i >= 0) i--

  if (i === -1) return '1' + slotName

  const chars = slotName.split('')
  chars[i] = String.fromCharCode(chars[i].charCodeAt(0) + 1)
  return chars.join('')
}

export function calculateNextSlotName (slotNames) {
  return next(max(slotNames))
}

export function limitColumnWidths (columns, minWidth = 3, maxWidth = 12) {
  const cols = cloneDeep(columns)

  if (cols.length < 1) return cols

  let excess = cols.reduce((sum, col) => sum + col.width, 0) - maxWidth
  let i = cols.length - 1
  while (excess > 0 && i >= 0) {
    const diff = Math.min(Math.max(0, cols[i].width - minWidth), excess)
    cols[i].width -= diff
    excess -= diff
    i--
  }

  return cols
}

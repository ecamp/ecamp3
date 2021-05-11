import cloneDeep from 'lodash/cloneDeep'

export function calculateNextSlotName (slotNames) {
  let i = 0
  while (true) {
    if (!slotNames.includes((++i).toString())) return i.toString()
  }
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

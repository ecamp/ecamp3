import cloneDeep from 'lodash/cloneDeep'

export function calculateNextSlotName (slotNames) {
  let i = 0
  // eslint-disable-next-line no-constant-condition
  while (true) {
    if (!slotNames.includes((++i).toString())) return i.toString()
  }
}

export function adjustColumnWidths (columns, minWidth = 3, maxWidth = 12) {
  const cols = cloneDeep(columns)

  if (cols.length < 1) return cols

  // Enforce minimum column widths
  cols.forEach((col) => {
    col.width = Math.max(minWidth, col.width)
  })

  // Make sure the column widths sum up to maxWidth
  let excess = cols.reduce((sum, col) => sum + col.width, 0) - maxWidth
  let i = cols.length - 1
  while (excess !== 0 && i >= 0) {
    const diff = Math.min(Math.max(0, cols[i].width - minWidth), excess)
    cols[i].width -= diff
    excess -= diff
    i--
  }

  return cols
}

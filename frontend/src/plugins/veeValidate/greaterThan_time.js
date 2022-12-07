function normalizeMin(min, dayjs) {
  return typeof min === 'string'
    ? dayjs.utc('1970-01-01 ' + min, 'YYYY-MM-DD LT')
    : min.set('date', 1).set('month', 0).set('year', 1970)
}

export default (dayjs, i18n) => ({
  params: ['min'],
  /**
   *
   * @param {string} value Time value in string format 'HH:mm'
   * @param {string} min   Comparison value in string format 'HH:mm'
   * @returns {bool}       validation result
   */
  validate: (value, { min }) => {
    const valueDate = dayjs.utc('1970-01-01 ' + value, 'YYYY-MM-DD LT')
    const minDate = normalizeMin(min, dayjs)
    return valueDate.unix() > minDate.unix()
  },
  message: (field, values) => {
    return i18n.tc('global.validation.greaterThan_time', 0, {
      min: normalizeMin(values.min, dayjs).format('LT'),
    })
  },
})

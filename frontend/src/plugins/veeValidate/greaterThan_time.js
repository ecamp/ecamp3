function normalizeMin(min, dayjs) {
  return typeof min === 'string'
    ? dayjs.utc('1970-01-01 ' + min, 'YYYY-MM-DD LT')
    : min.set('date', 1).set('month', 0).set('year', 1970)
}

export default (dayjs, i18n) =>
  /**
   *
   * @param {string} value Time value in string format 'HH:mm'
   * @param {string} min   Comparison value in string format 'HH:mm'
   * @param {string} label Field label
   * @returns {boolean}       validation result
   */
  (value, [min], { label }) => {
    if (value === '' || value == null) return true

    const valueDate = dayjs.utc('1970-01-01 ' + value, 'YYYY-MM-DD LT')
    const minDate = normalizeMin(min, dayjs)
    const validate = valueDate.unix() > minDate.unix()

    if (validate) {
      return true
    }

    return i18n.global.t('global.validation.greaterThan_time', {
      min: normalizeMin(min, dayjs).format('LT'),
      field: label,
    })
  }

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

    const valueDate = dayjs.utc(value, 'YYYY-MM-DD LT')
    const minDate = dayjs.utc(min, 'YYYY-MM-DD LT')
    const validate = valueDate.unix() > minDate.unix()

    if (validate) {
      return true
    }

    return i18n.global.t('global.validation.greaterThan_time', {
      min: minDate.format('LT'),
      field: label,
    })
  }

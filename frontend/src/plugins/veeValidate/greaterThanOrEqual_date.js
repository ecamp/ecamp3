export default (dayjs, i18n) =>
  /**
   * @param   {string}  value Dater value in local string format
   * @param   {string}  min   comparison valye in local string format
   * @param {string} label Field label
   * @returns {boolean}       validation result
   */
  (value, [min], { label }) => {
    if (value === '' || value == null) return true

    const minDate = dayjs.utc(min, 'L')
    const valueDate = dayjs.utc(value, 'L')
    const validate = valueDate.diff(minDate, 'day') >= 0

    if (validate) {
      return true
    }

    return i18n.global.t('global.validation.greaterThanOrEqual_date', {
      min,
      field: label,
    })
  }

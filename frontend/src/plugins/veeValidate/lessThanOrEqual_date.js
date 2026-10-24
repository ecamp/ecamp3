export default (dayjs, i18n) =>
  /**
   * @param   {string}  value Date value in local string format
   * @param   {string}  max   comparison value in local string format
   * @param {string} label Field label
   * @returns {boolean}       validation result
   */
  (value, [max], { label }) => {
    if (value === '' || value == null) return true

    const maxDate = dayjs.utc(max, 'L')
    const valueDate = dayjs.utc(value, 'L')
    const validate = valueDate.diff(maxDate, 'day') <= 0

    if (validate) {
      return true
    }

    return i18n.global.t('global.validation.lessThanOrEqual_date', {
      max,
      field: label,
    })
  }

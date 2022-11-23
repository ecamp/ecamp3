export default (dayjs, i18n) => ({
  params: ['min'],
  /**
   *
   * @param {string} value Time value in string format 'HH:mm'
   * @param {string} min   Comparison value in string format 'HH:mm'
   * @returns {bool}       validation result
   */
  validate: (value, { min }) => {
    const minDate = dayjs('1970-01-01 ' + min)
    const valueDate = dayjs('1970-01-01 ' + value)
    return valueDate > minDate
  },
  message: (field, values) => i18n.tc('global.validation.greaterThan_time', 0, values),
})

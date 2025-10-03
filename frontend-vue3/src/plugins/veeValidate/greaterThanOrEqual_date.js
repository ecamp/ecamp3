export default (dayjs, i18n) => ({
  params: ['min'],
  /**
   * @param   {string}  value Dater value in local string format
   * @param   {string}  min   comparison valye in local string format
   * @returns {bool}          validation result
   */
  validate: (value, { min }) => {
    const minDate = dayjs.utc(min, 'L')
    const valueDate = dayjs.utc(value, 'L')
    return valueDate.diff(minDate, 'day') >= 0
  },
  message: (field, values) =>
    i18n.tc('global.validation.greaterThanOrEqual_date', 0, values),
})

export default (dayjs, i18n) => ({
  params: ['max'],
  /**
   * @param   {string}  value Dater value in local string format
   * @param   {string}  max   comparison valye in local string format
   * @returns {bool}          validation result
   */
  validate: (value, { max }) => {
    const maxDate = dayjs.utc(max, 'L')
    const valueDate = dayjs.utc(value, 'L')
    return valueDate.diff(maxDate, 'day') <= 0
  },
  message: (field, values) =>
    i18n.tc('global.validation.lessThanOrEqual_date', 0, values),
})

export default (i18n) => ({
  params: ['min'],
  /**
   *
   * @param {string} value value of a float number
   * @param number min   Comparison value in string format 'HH:mm'
   * @returns {boolean}       validation result
   */
  validate: (value, { min }) => {
    return parseFloat(value) > min
  },
  message: (field, { min }) => {
    return i18n.tc('global.validation.greaterThan', 0, {
      min: min,
    })
  },
})

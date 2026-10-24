export const validationMessageKey = 'global.validation.greaterThan'
export default (i18n) =>
  /**
   * @param {string} value value of a float number
   * @param number min   Comparison value (interpreted as float)
   * @param {string} label Field label
   * @returns {boolean}       validation result
   */
  (value, [min], { label }) => {
    if (value === '' || value == null) return true

    const validate = parseFloat(value) > min

    if (validate) {
      return true
    }
    return i18n.global.t(validationMessageKey, {
      min: min,
      field: label,
    })
  }

import { size } from 'lodash-es'

export default (i18n) =>
  (value, _, { label }) => {
    if (value === '' || value == null) return true

    const validate = /\p{Extended_Pictographic}/u.test(value)
      ? size(value) <= 1
      : value.length <= 2

    if (validate) {
      return true
    }

    return i18n.global.t('global.validation.oneEmojiOrTwoCharacters', {
      field: label,
    })
  }

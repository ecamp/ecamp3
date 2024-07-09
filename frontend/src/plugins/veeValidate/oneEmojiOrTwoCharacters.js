import { size } from 'lodash'

export default (i18n) => ({
  validate: (value) => {
    return /\p{Extended_Pictographic}/u.test(value) ? size(value) <= 1 : value.length <= 2
  },
  message: (field, values) =>
    i18n.tc('global.validation.oneEmojiOrTwoCharacters', 0, values),
})

import { zxcvbnAsync, zxcvbnOptions } from '@zxcvbn-ts/core'
import { adjacencyGraphs, dictionary } from '@zxcvbn-ts/language-common'
import * as en from '@zxcvbn-ts/language-en'
import * as de from '@zxcvbn-ts/language-de'
import * as fr from '@zxcvbn-ts/language-fr'
import * as it from '@zxcvbn-ts/language-it'

const languages = { en, de, fr, it }

const options = function (lang) {
  const baseLanguage = lang.split('-', 2)[0]
  return {
    translations: languages[baseLanguage].translations,
    graphs: adjacencyGraphs,
    dictionary: {
      ...dictionary,
      ...languages[baseLanguage].dictionary,
    },
  }
}

export const passwordStrength = async function (password, lang) {
  zxcvbnOptions.setOptions(options(lang))
  return await zxcvbnAsync(password)
}

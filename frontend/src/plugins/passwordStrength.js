import { zxcvbnAsync, zxcvbnOptions } from '@zxcvbn-ts/core'
import zxcvbnCommonPackage from '@zxcvbn-ts/language-common'
import en from '@zxcvbn-ts/language-en'
import de from '@zxcvbn-ts/language-de'
import fr from '@zxcvbn-ts/language-fr'
import it from '@zxcvbn-ts/language-it'

const languages = { en, de, fr, it }

const options = function (lang) {
  const baseLanguage = lang.split('-', 2)[0]
  return {
    translations: languages[baseLanguage].translations,
    graphs: zxcvbnCommonPackage.adjacencyGraphs,
    dictionary: {
      ...zxcvbnCommonPackage.dictionary,
      ...languages[baseLanguage].dictionary,
    },
  }
}

export const passwordStrength = async function (password, lang) {
  zxcvbnOptions.setOptions(options(lang))
  return await zxcvbnAsync(password)
}

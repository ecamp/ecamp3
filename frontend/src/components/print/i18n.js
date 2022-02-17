import { translate, createCoreContext, registerMessageCompiler, compileToFunction } from '@intlify/core'

const createI18n = (translationData, language) => {
  registerMessageCompiler(compileToFunction)

  const context = createCoreContext({
    locale: language,
    fallbackLocale: 'en',
    messages: translationData,
    missingWarn: false,
    fallbackWarn: false
  })

  return {
    translate: (...args) => {
      return translate(context, ...args)
    }
  }
}

export default createI18n

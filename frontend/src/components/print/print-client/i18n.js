import {
  compileToFunction,
  createCoreContext,
  fallbackWithLocaleChain,
  registerLocaleFallbacker,
  registerMessageCompiler,
  resolveValue,
  translate,
} from '@intlify/core'

const createI18n = (translationData, language) => {
  registerMessageCompiler(compileToFunction)
  registerLocaleFallbacker(fallbackWithLocaleChain)

  const context = createCoreContext({
    locale: language,
    fallbackLocale: 'en',
    messages: translationData,
    missingWarn: false,
    fallbackWarn: false,
    messageResolver: resolveValue,
  })

  return {
    translate: (...args) => {
      return translate(context, ...args)
    },
  }
}

export default createI18n

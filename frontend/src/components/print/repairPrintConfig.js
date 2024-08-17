import cloneDeep from 'lodash/cloneDeep'
import campShortTitle from '@/common/helpers/campShortTitle.js'

export default function repairConfig(
  config,
  camp,
  availableLocales,
  fallbackLocale,
  componentRepairers,
  defaultContents
) {
  const configClone = config ? cloneDeep(config) : {}
  if (!availableLocales.includes(configClone.language)) {
    configClone.language = availableLocales.includes(fallbackLocale)
      ? fallbackLocale
      : availableLocales.length > 0
        ? availableLocales[0]
        : 'en'
  }
  if (!configClone.documentName) configClone.documentName = campShortTitle(camp)
  if (configClone.camp !== camp._meta.self) configClone.camp = camp._meta.self
  if (typeof configClone.contents?.map !== 'function') {
    configClone.contents = defaultContents
  }
  configClone.contents = configClone.contents
    .map((content) => {
      if (!content.type || !(content.type in componentRepairers)) return null
      const componentRepairer = componentRepairers[content.type]
      if (typeof componentRepairer !== 'function') return content
      return componentRepairer(content, camp)
    })
    .filter((component) => component)

  return configClone
}

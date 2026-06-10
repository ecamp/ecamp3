import { cloneDeep } from 'lodash-es'
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
  if (!configClone.options || typeof configClone.options !== 'object') {
    configClone.options = {}
  }
  if (
    configClone.options.pageNumbers !== true &&
    configClone.options.pageNumbers !== false
  ) {
    configClone.options.pageNumbers = false
  }
  if (!['A5', 'A4', 'A3'].includes(configClone.options.pageSize)) {
    configClone.options.pageSize = 'A4'
  }
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

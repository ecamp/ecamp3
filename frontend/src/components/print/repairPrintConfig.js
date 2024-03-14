export default function repairConfig(
  config,
  camp,
  availableLocales,
  componentRepairers,
  defaultContents
) {
  if (!config) config = {}
  if (!availableLocales.includes(config.language)) config.language = 'en'
  if (!config.documentName) config.documentName = camp.name
  if (config.camp !== camp._meta.self) config.camp = camp._meta.self
  if (typeof config.contents?.map !== 'function') {
    config.contents = defaultContents
  }
  config.contents = config.contents
    .map((content) => {
      if (!content.type || !(content.type in componentRepairers)) return null
      const componentRepairer = componentRepairers[content.type]
      if (typeof componentRepairer !== 'function') return content
      return componentRepairer(content, camp)
    })
    .filter((component) => component)

  return config
}

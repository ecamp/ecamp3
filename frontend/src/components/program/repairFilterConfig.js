import { isEqual } from 'lodash-es'

export default function repairFilterConfig(filter, camp) {
  if (!filter || typeof filter !== 'object') {
    return {
      period: null,
      category: [],
      day: [],
      responsible: [],
      progressLabel: [],
      activityCount: 0,
    }
  }
  if (!filter.period) filter.period = null
  if (!filter.day) filter.day = []
  if (!camp.periods()._meta.loading) {
    const knownPeriods = camp.periods().items.map((p) => p._meta.self)
    if (!knownPeriods.includes(filter.period)) filter.period = null

    if (camp.periods().items.every((period) => !period.days()._meta.loading)) {
      const knownDays = camp
        .periods()
        .items.flatMap((period) => period.days().items)
        .map((d) => d._meta.self)
      filter.day = filter.day.filter((day) => {
        return knownDays.includes(day)
      })
    }
  }

  if (!filter.category) filter.category = []
  if (!camp.categories()._meta.loading) {
    const knownCategories = camp.categories().items.map((c) => c._meta.self)
    filter.category = filter.category.filter((category) => {
      return knownCategories.includes(category)
    })
  }

  if (!filter.responsible) filter.responsible = []
  if (!camp.campCollaborations()._meta.loading) {
    const knownCampCollaborations = camp
      .campCollaborations()
      .items.map((c) => c._meta.self)
    if (!isEqual(filter.responsible, ['none'])) {
      filter.responsible = filter.responsible.filter((responsible) => {
        return knownCampCollaborations.includes(responsible)
      })
    }
  }

  if (!filter.progressLabel) filter.progressLabel = []
  if (!camp.progressLabels()._meta.loading) {
    const knownProgressLabels = camp.progressLabels().items.map((l) => l._meta.self)
    filter.progressLabel = filter.progressLabel.filter((progressLabel) => {
      return knownProgressLabels.includes(progressLabel) || 'none' === progressLabel
    })
  }

  if (!filter.activityCount) filter.activityCount = 0
  return filter
}

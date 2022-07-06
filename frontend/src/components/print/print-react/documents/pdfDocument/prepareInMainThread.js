const picassoData = (config) => {
  if (!config.contents.some((c) => c.type === 'Picasso')) {
    return []
  }

  const camp = config.apiGet(config.camp)

  return [
    camp._meta.load,
    camp.categories().$loadItems(),
    camp
      .activities()
      .$loadItems()
      .then((activities) => {
        return Promise.all(
          activities.items.map((activity) => {
            return activity.activityResponsibles().$loadItems()
          })
        )
      }),
    camp
      .campCollaborations()
      .$loadItems()
      .then((campCollaboration) => {
        return campCollaboration.user
          ? campCollaboration.user()._meta.load
          : Promise.resolve()
      }),
    camp
      .periods()
      .$loadItems()
      .then((periods) => {
        return Promise.all(
          periods.items.map((period) => {
            return Promise.all([
              period.scheduleEntries().$loadItems(),
              period.contentNodes().$loadItems()
            ])
          })
        )
      }),
  ]
}

const activityData = (config) => {
  if (!config.contents.some((c) => ['Program', 'Activity'].includes(c.type))) {
    return []
  }

  const camp = config.apiGet(config.camp)

  return [
    camp._meta.load,
    camp.categories().$loadItems(),
    camp
      .activities()
      .$loadItems()
      .then((activities) => {
        return Promise.all(
          activities.items.map((activity) => {
            return activity.activityResponsibles().$loadItems()
          })
        )
      }),
    camp
      .campCollaborations()
      .$loadItems()
      .then((campCollaboration) => {
        return campCollaboration.user
          ? campCollaboration.user()._meta.load
          : Promise.resolve()
      }),
    camp
      .periods()
      .$loadItems()
      .then((periods) => {
        return Promise.all(
          periods.items.map((period) => {
            return Promise.all([
              period.scheduleEntries().$loadItems(),
              period.contentNodes().$loadItems()
            ])
          })
        )
      }),
    camp.materialLists().$loadItems(),
    config.apiGet().contentTypes().$loadItems(),
  ]
}

const loadData = async (config) => {
  // Load any data necessary based on the print config
  return Promise.all([...picassoData(config), ...activityData(config)])
}

const prepareInMainThread = async (config) => {
  return await loadData(config)
}

export default prepareInMainThread

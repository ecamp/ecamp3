const loadData = async (config) => {
  // Load any data necessary based on the print config
  return Promise.all([
    config.camp()._meta.load,
    config.camp().categories().$loadItems(),
    config.camp().activities().$loadItems().then(activities => {
      return Promise.all(activities.items.map(activity => {
        return Promise.all([
          activity.activityResponsibles().$loadItems(),
          activity.contentNodes().$loadItems()
        ])
      }))
    }),
    config.camp().campCollaborations().$loadItems().then(campCollaboration => {
      return campCollaboration.user ? campCollaboration.user()._meta.load : Promise.resolve()
    }),
    config.camp().periods().$loadItems().then(periods => {
      return Promise.all(periods.items.map(period => {
        return period.scheduleEntries().$loadItems()
      }))
    }),
    config.camp().materialLists().$loadItems(),
    config.apiGet().contentTypes().$loadItems()
  ])
}

const prepareInMainThread = async (config) => {
  return await loadData(config)
}

export default prepareInMainThread

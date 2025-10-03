async function prepareInMainThread(config) {
  const picassoData = (config2) => {
    const camp = config2.apiGet(config2.camp);
    return [
      camp._meta.load,
      camp.categories().$loadItems(),
      camp.activities().$loadItems().then((activities) => {
        return Promise.all(
          activities.items.map((activity) => {
            return activity.activityResponsibles().$loadItems();
          })
        );
      }),
      camp.campCollaborations().$loadItems().then((campCollaborations) => {
        return Promise.all(
          campCollaborations.items.map((campCollaboration) => {
            return campCollaboration.user ? campCollaboration.user()._meta.load : Promise.resolve();
          })
        );
      }),
      camp.periods().$loadItems().then((periods) => {
        return Promise.all(
          periods.items.map((period) => {
            return Promise.all([
              period.scheduleEntries().$loadItems(),
              period.contentNodes().$loadItems(),
              period.days().$loadItems(),
              period.dayResponsibles().$loadItems()
            ]);
          })
        );
      }),
      camp.profiles().$loadItems()
    ];
  };
  const activityData = (config2) => {
    if (!config2.contents.some(
      (c) => ["Program", "Activity", "ActivityList"].includes(c.type)
    )) {
      return [];
    }
    const camp = config2.apiGet(config2.camp);
    return [
      camp._meta.load,
      camp.categories().$loadItems(),
      camp.activities().$loadItems().then((activities) => {
        return Promise.all(
          activities.items.map((activity) => {
            return activity.activityResponsibles().$loadItems();
          })
        );
      }),
      camp.campCollaborations().$loadItems().then((campCollaboration) => {
        return campCollaboration.user ? campCollaboration.user()._meta.load : Promise.resolve();
      }),
      camp.periods().$loadItems().then((periods) => {
        return Promise.all(
          periods.items.map((period) => {
            return Promise.all([
              period.scheduleEntries().$loadItems(),
              period.contentNodes().$loadItems()
            ]);
          })
        );
      }),
      camp.materialLists().$loadItems(),
      config2.apiGet().contentTypes().$loadItems(),
      camp.checklists().$loadItems(),
      config2.apiGet().checklistItems({
        "checklist.camp": camp._meta.self
      }).$loadItems()
    ];
  };
  const loadData = async (config2) => {
    return Promise.all([...picassoData(config2), ...activityData(config2)]);
  };
  return await loadData(config);
}
export {
  prepareInMainThread
};

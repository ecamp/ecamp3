import { firstActivityScheduleEntry } from '@/router.js'
import { apiStore } from '@/plugins/store'

/**
 * Loads first valid schedule entry for activity if current one is missing
 * @param activity
 * @param to
 * @param from
 * @param next
 * @return {Promise<*>}
 */
export default async function scheduleEntryRouteChange(activity, to, from, next) {
  if (
    to.params.scheduleEntryId !== from.params.scheduleEntryId ||
    to.params.activityId !== from.params.activityId
  ) {
    apiStore.get().activities({ id: to.params.activityId }).$reload()
    // activity reload doesn't need to be awaited, but for scheduleEntry we want to
    // ensure it exists and otherwise reroute
    return await apiStore
      .get()
      .scheduleEntries({ id: to.params.scheduleEntryId })
      .$reload()
      .then(() => next())
      .catch(async () => {
        return next({
          name: 'camp/activity',
          params: {
            ...to.params,
            scheduleEntryId: (await firstActivityScheduleEntry(to.params.activityId)).id,
          },
        })
      })
  } else {
    return next()
  }
}

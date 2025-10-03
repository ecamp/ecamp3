/**
 * merges multiple listeners objects into a single one that is properly interpreted by Vue
 * valuable to merge listeners from multiple composables
 *
 * Example Input:
 * [
 *  {
 *    'click': clickCallback1,
 *    'mousedown': mousedownCallback1,
 *  },
 *  {
 *    'click': clickCallback2
 *  }
 * ]
 *
 * Example Output:
 * {
 *   'click':[
 *      clickCallback1,
 *      clickCallback2
 *   ],
 *   'mousedown': [
 *      mousedownCallback1
 *   ]
 * }
 */
export default function (listenersList) {
  const mergedListeners = {}

  listenersList.forEach((listeners) => {
    for (const [eventName, callback] of Object.entries(listeners)) {
      if (mergedListeners[eventName] === undefined) {
        mergedListeners[eventName] = []
      }

      mergedListeners[eventName].push(callback)
    }
  })

  return mergedListeners
}

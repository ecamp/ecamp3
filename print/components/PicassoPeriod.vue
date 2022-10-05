<template>
  <div>
    <generic-error-message v-if="$fetchState.error" :error="$fetchState.error" />
    <picasso-chunk
      v-for="(periodChunk, i) in periodChunks"
      v-else
      :key="i"
      :period="period"
      :start="periodChunk.start.format('YYYY-MM-DD')"
      :end="periodChunk.end.format('YYYY-MM-DD')"
      :events="events"
      :index="index"
    />
  </div>
</template>

<script>
import dayjs from '@/../common/helpers/dayjs.js'
import { utcStringToTimestamp } from '~/../common/helpers/dateHelperVCalendar.js'

export default {
  props: {
    period: { type: Object, required: true },
    camp: { type: Object, required: true },
    orientation: { type: String, required: true },
    index: { type: Number, required: true },
  },
  data() {
    return {
      events: null,
    }
  },
  async fetch() {
    const [scheduleEntries] = await Promise.all([
      this.period.scheduleEntries().$loadItems(),
      this.camp
        .activities()
        .$loadItems()
        .then((activities) => {
          return Promise.all(
            activities.items.map((activity) =>
              activity
                .activityResponsibles()
                .$loadItems()
                .then((activityResponsibles) => {
                  return Promise.all(
                    activityResponsibles.items.map((activityResponsible) => {
                      if (activityResponsible.campCollaboration().user === null) {
                        return Promise.resolve(null)
                      }
                      return activityResponsible.campCollaboration().user()._meta.load
                    })
                  )
                })
            )
          )
        }),
      this.camp.categories().$loadItems(),
    ])

    this.events = scheduleEntries.items.map((entry) => ({
      ...entry,
      startTimestamp: utcStringToTimestamp(entry.start),
      endTimestamp: utcStringToTimestamp(entry.end),
      timed: true,
    }))
  },
  computed: {
    periodChunks() {
      // TODO: choose chunkSize based on orientation
      const chunkSize = 3

      const start = dayjs.utc(this.period.start)
      const end = dayjs.utc(this.period.end)
      const hours = end.diff(start, 'hours')
      const days = Math.floor(hours / 24) + 1

      const numberOfChunks = Math.ceil(days / chunkSize)

      return [...Array(numberOfChunks).keys()].map((i) => {
        return {
          start: start.add(i * chunkSize, 'days'),
          end: start.add((i + 1) * chunkSize - 1, 'days'),
        }
      })
    },
  },
}
</script>

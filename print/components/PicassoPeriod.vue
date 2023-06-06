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
      :landscape="landscape"
    />
  </div>
</template>

<script>
import { utcStringToTimestamp } from '~/../common/helpers/dateHelperVCalendar.js'
import { splitDaysIntoPages } from '../../common/helpers/picasso.js'

export default {
  props: {
    period: { type: Object, required: true },
    camp: { type: Object, required: true },
    landscape: { type: Boolean, required: true },
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
      this.period
        .days()
        .$loadItems()
        .then((days) => {
          return Promise.all(
            days.items.map((day) =>
              day
                .dayResponsibles()
                .$loadItems()
                .then((dayResponsibles) => {
                  return Promise.all(
                    dayResponsibles.items.map((dayResponsible) => {
                      if (dayResponsible.campCollaboration().user === null) {
                        return Promise.resolve(null)
                      }
                      return dayResponsible.campCollaboration().user()._meta.load
                    })
                  )
                })
            )
          )
        }),
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
      const chunkSize = this.landscape ? 7 : 4

      return splitDaysIntoPages(this.period.days().items, chunkSize).map((chunk) => ({
        start: this.$date.utc(chunk[0].start),
        end: this.$date.utc(chunk[chunk.length - 1].start),
      }))
    },
  },
}
</script>

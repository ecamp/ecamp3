<template>
  <div>
    <generic-error-message v-if="$fetchState.error" :error="$fetchState.error" />

    <picasso-chunk
      v-for="(pageDays, i) in pages"
      :key="i"
      :period="period"
      :schedule-entries="period.scheduleEntries().items"
      :index="index"
      :landscape="landscape"
      :days="pageDays"
      :times="times"
    />
  </div>
</template>

<script>
import {
  splitDaysIntoPages,
  calculateBedtime,
  times,
} from '@/../common/helpers/picasso.js'
import { sortBy } from 'lodash'

export default {
  props: {
    period: { type: Object, required: true },
    camp: { type: Object, required: true },
    landscape: { type: Boolean, required: true },
    index: { type: Number, required: true },
  },
  async fetch() {
    await Promise.all([
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
      this.period.days().$loadItems(),
      this.period
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
        }),
    ])
  },
  computed: {
    days() {
      return sortBy(this.period.days().items, (day) =>
        this.$date.utc(day.start).valueOf()
      )
    },
    pages() {
      const maxDaysPerPage = this.landscape ? 7 : 4
      return splitDaysIntoPages(this.days, maxDaysPerPage)
    },
    timeStep() {
      // Height / duration of each picasso row, in hours
      return 1
    },
    bedtimes() {
      return calculateBedtime(
        this.period.scheduleEntries().items,
        this.$date,
        this.$date.utc(this.days[0].start),
        this.$date.utc(this.days[this.days.length - 1].end),
        this.timeStep
      )
    },
    times() {
      return times(this.bedtimes.getUpTime, this.bedtimes.bedtime, this.timeStep)
    },
  },
}
</script>

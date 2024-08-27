<template>
  <div>
    <generic-error-message v-if="error" :error="error" />

    <picasso-chunk
      v-for="(pageDays, i) in pages"
      :key="i"
      :period="period"
      :schedule-entries="period.scheduleEntries().items"
      :index="index"
      :landscape="landscape"
      :days="pageDays"
      :times="timesList"
    />
  </div>
</template>

<script setup>
const props = defineProps({
  period: { type: Object, required: true },
  camp: { type: Object, required: true },
  landscape: { type: Boolean, required: true },
  index: { type: Number, required: true },
})

const { error } = await useAsyncData(
  `PicassoPeriod-${props.period._meta.self}`,
  async () => {
    return await Promise.all([
      props.period.scheduleEntries().$loadItems(),
      props.camp
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
      props.camp.categories().$loadItems(),
      props.period.days().$loadItems(),
      props.period
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
  }
)
</script>

<script>
import { splitDaysIntoPages, calculateBedtime, times } from '@/common/helpers/picasso.js'
import sortBy from 'lodash/sortBy.js'

export default {
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
    timesList() {
      return times(this.bedtimes.getUpTime, this.bedtimes.bedtime, this.timeStep)
    },
  },
}
</script>

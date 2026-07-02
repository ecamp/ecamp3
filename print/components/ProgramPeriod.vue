<template>
  <div class="tw-break-after-page" :class="pageSize">
    <div>
      <h1 class="tw-text-center tw-font-semibold tw-mb-6">
        {{ $t('print.program.title') }}: {{ period.description }}
      </h1>
    </div>
    <generic-error-message v-if="error" :error="error" />
    <program-day
      v-for="{
        day,
        scheduleEntries,
        summaryScheduleEntries: daySummaryScheduleEntries,
      } in days"
      v-else
      :key="'day' + day.id"
      :day="day"
      :filter="filter"
      :show-daily-summary="showDailySummary"
      :show-activities="showActivities"
      :index="index"
      :schedule-entries="scheduleEntries"
      :summary-schedule-entries="daySummaryScheduleEntries"
    />
  </div>
</template>

<script setup>
import { filterMatchScheduleEntry } from '@/common/helpers/filterMatchScheduleEntry.js'
import { filterScheduleEntriesByDay } from '@/common/helpers/picasso.js'

const FULL_DAY_TIMES = [
  [0, 1],
  [24, 0],
]

const { $date } = useNuxtApp()

const props = defineProps({
  period: { type: Object, required: true },
  filter: { type: Object, default: () => ({}) },
  showDailySummary: { type: Boolean, required: true },
  showActivities: { type: Boolean, required: true },
  index: { type: Number, required: true },
  pageSize: { type: String, default: 'a4' },
})

const { data: days, error } = await useAsyncData(
  `ProgramPeriod-${props.period._meta.self}`,
  async () => {
    await Promise.all([
      props.period.days().$loadItems(),
      props.period.scheduleEntries().$loadItems(),
      props.period.contentNodes().$loadItems(),
    ])

    if (props.showDailySummary) {
      const activities = [
        ...new Map(
          props.period
            .scheduleEntries()
            .items.map((scheduleEntry) => [
              scheduleEntry.activity()._meta.self,
              scheduleEntry.activity(),
            ])
        ).values(),
      ]

      await Promise.all([
        ...activities.map((activity) =>
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
        ),
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

    const scheduleEntries = props.period.scheduleEntries().items
    const days = filteredDays(props.period.days().items, props.filter)
    const programScheduleEntries = getProgramScheduleEntries(scheduleEntries, days)

    return days
      .map((day) => ({
        day,
        scheduleEntries: getScheduleEntriesForDay(day, days, programScheduleEntries),
        summaryScheduleEntries:
          props.showDailySummary && dayMatchesFilter(day, props.filter)
            ? getSummaryScheduleEntries(day, scheduleEntries)
            : [],
      }))
      .filter(({ scheduleEntries, summaryScheduleEntries }) => {
        if (props.showDailySummary) {
          return summaryScheduleEntries.length > 0
        }

        return scheduleEntries.length > 0
      })
  }
)

function filteredDays(days, filter) {
  return days.filter((day) => dayMatchesFilter(day, filter))
}

function dayMatchesFilter(day, filter) {
  return (
    filter.day === null ||
    filter.day === undefined ||
    filter.day.length === 0 ||
    filter.day.includes(day._meta.self)
  )
}

function getSummaryScheduleEntries(day, scheduleEntries) {
  return filterScheduleEntriesByDay(
    scheduleEntries.filter((scheduleEntry) =>
      filterMatchScheduleEntry(scheduleEntry, { ...props.filter, day: [] })
    ),
    day,
    FULL_DAY_TIMES
  )
}

function getProgramScheduleEntries(scheduleEntries, days) {
  const filteredScheduleEntries = scheduleEntries.filter((scheduleEntry) =>
    filterMatchScheduleEntry(scheduleEntry, { ...props.filter, day: [] })
  )

  if (!hasDayFilter(props.filter)) return filteredScheduleEntries

  return filteredScheduleEntries.filter((scheduleEntry) =>
    days.some(
      (day) => filterScheduleEntriesByDay([scheduleEntry], day, FULL_DAY_TIMES).length
    )
  )
}

function getScheduleEntriesForDay(day, days, scheduleEntries) {
  const previousDays = days.filter((otherDay) =>
    $date.utc(otherDay.start).isBefore($date.utc(day.start))
  )

  return filterScheduleEntriesByDay(scheduleEntries, day, FULL_DAY_TIMES).filter(
    (scheduleEntry) =>
      !previousDays.some(
        (previousDay) =>
          filterScheduleEntriesByDay([scheduleEntry], previousDay, FULL_DAY_TIMES).length
      )
  )
}

function hasDayFilter(filter) {
  return filter.day !== null && filter.day !== undefined && filter.day.length > 0
}
</script>

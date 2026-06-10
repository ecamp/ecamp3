<template>
  <div>
    <div v-if="showDailySummary">
      <h1
        class="tw-text-center tw-text-3xl tw-mb-6 tw-bg-black tw-text-white tw-p-2 tw-flex tw-justify-between"
      >
        <span>{{ $t('entity.day.name') }} {{ day.number }}</span>
        <span>{{ dateLong(day.start) }}</span>
      </h1>
      <p v-if="dayResponsibles" class="tw-text-lg">
        <strong class="tw-font-medium"
          >{{ $t('entity.day.fields.dayResponsibles') }}:</strong
        >
        {{ dayResponsibles }}
      </p>
      <table v-if="scheduleEntries.length" class="schedule-entries-table">
        <thead>
          <tr>
            <th>{{ $t('entity.scheduleEntry.fields.time') }}</th>
            <th>{{ $t('entity.scheduleEntry.fields.nr') }}</th>
            <th>{{ $t('entity.activity.fields.title') }}</th>
            <th>{{ $t('entity.activity.fields.responsible') }}</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="scheduleEntry in scheduleEntries" :key="scheduleEntry.id">
            <td class="tw-tabular-nums">
              {{ rangeTime(scheduleEntry.start, scheduleEntry.end) }}
            </td>
            <td class="tw-tabular-nums">{{ scheduleEntry.number }}</td>
            <td>
              <span class="tw-inline-flex tw-items-center tw-gap-2">
                <category-label :category="scheduleEntry.activity().category()" />
                <span>{{ scheduleEntry.activity().title }}</span>
              </span>
            </td>
            <td>{{ activityResponsibles(scheduleEntry.activity()) }}</td>
          </tr>
        </tbody>
      </table>
    </div>

    <div v-if="showActivities">
      <schedule-entry
        v-for="scheduleEntry in scheduleEntries"
        :key="scheduleEntry.id"
        :schedule-entry="scheduleEntry"
        :index="index"
      />
    </div>
  </div>
</template>

<script>
import CategoryLabel from '@/components/generic/CategoryLabel.vue'
import { dateHelperUTCFormatted } from '@/mixins/dateHelperUTCFormatted.js'
import { filterMatchScheduleEntry } from '../common/helpers/filterMatchScheduleEntry.js'
import { dayResponsiblesCommaSeparated } from '@/common/helpers/dayResponsibles.js'
import { activityResponsiblesCommaSeparated } from '@/common/helpers/activityResponsibles.js'

export default {
  components: { CategoryLabel },
  mixins: [dateHelperUTCFormatted],
  props: {
    day: { type: Object, required: true },
    filter: { type: Object, default: () => ({}) },
    showDailySummary: { type: Boolean, required: true },
    showActivities: { type: Boolean, required: true },
    index: { type: Number, required: true },
  },
  computed: {
    dayResponsibles() {
      return dayResponsiblesCommaSeparated(this.day, this.$t.bind(this))
    },
    // returns scheduleEntries of current day without the need for an additional API call
    scheduleEntries() {
      return this.day
        .period()
        .scheduleEntries()
        .items.filter((scheduleEntry) => {
          return (
            scheduleEntry.day()._meta.self === this.day._meta.self &&
            filterMatchScheduleEntry(scheduleEntry, this.filter)
          )
        })
    },
  },
  methods: {
    activityResponsibles(activity) {
      return activityResponsiblesCommaSeparated(activity, this.$t.bind(this))
    },
    rangeTime(start, end) {
      return `${this.$date.utc(start).format('HH:mm')} - ${this.$date
        .utc(end)
        .format('HH:mm')}`
    },
  },
}
</script>

<style lang="scss" scoped>
.schedule-entries-table {
  border-collapse: collapse;
  margin: 1rem 0 2rem;
  width: 100%;
  text-align: left;
}

.schedule-entries-table tr {
  break-inside: avoid;
}

.schedule-entries-table th,
.schedule-entries-table td {
  border-bottom: 1px solid #d1d5db;
  padding: 0.35rem 0.5rem;
}

.schedule-entries-table th {
  border-bottom: 1px solid black;
  font-weight: 600;
}

.schedule-entries-table th:first-child,
.schedule-entries-table td:first-child {
  white-space: nowrap;
  width: 1%;
  padding-left: 0;
}

.schedule-entries-table th:nth-child(2),
.schedule-entries-table td:nth-child(2) {
  white-space: nowrap;
  width: 1%;
}
</style>

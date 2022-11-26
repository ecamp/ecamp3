<template>
  <li class="tw-mb-1">
    {{ $tc('entity.day.name') }} {{ day.number }} ({{ dateLong(day.start) }})

    <ul>
      <toc-schedule-entry
        v-for="scheduleEntry in scheduleEntries"
        :key="scheduleEntry.id"
        :schedule-entry="scheduleEntry"
        :index="index"
      />
    </ul>
  </li>
</template>

<script>
import { dateHelperUTCFormatted } from '@/mixins/dateHelperUTCFormatted.js'

export default {
  name: 'TocProgramDay',
  mixins: [dateHelperUTCFormatted],
  props: {
    index: { type: Number, required: true },
    day: { type: Object, required: true },
  },
  computed: {
    // returns scheduleEntries of current day without the need for an additional API call
    scheduleEntries() {
      return this.day
        .period()
        .scheduleEntries()
        .items.filter((scheduleEntry) => {
          return scheduleEntry.day()._meta.self === this.day._meta.self
        })
    },
  },
}
</script>

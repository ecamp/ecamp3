<template>
  <li>
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
import { dateLong } from '@/../common/helpers/dateHelperUTCFormatted.js'

export default {
  name: 'TocProgramDay',
  props: {
    index: { type: Number, required: true },
    day: { type: Object, required: true },
  },
  data() {
    return {
      scheduleEntries: null,
    }
  },
  async fetch() {
    this.scheduleEntries = (await this.day.scheduleEntries()._meta.load).items
  },
  methods: {
    dateLong,
  },
}
</script>

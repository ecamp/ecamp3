<template>
  <template v-if="anyScheduleEntries">
    <Link class="toc-entry" :href="`#${id}-${period.id}`">
      <Text>{{ period.description }}</Text>
      <TocEntryPageNumber v-if="config.options.pageNumbers" :id="`${id}-${period.id}`" />
    </Link>
    <template v-for="{ day, scheduleEntries: dayScheduleEntries } in days">
      <View class="toc-entry toc-sub-entry toc-day-entry">
        <Text>{{ $tc('entity.day.name') }} {{ day.number }} ({{ date(day) }})</Text>
      </View>
      <Link
        v-for="scheduleEntry in dayScheduleEntries"
        class="toc-entry toc-sub-entry toc-activity-entry"
        :href="`#${id}-${period.id}-${scheduleEntry.id}`"
      >
        <Text>
          {{ scheduleEntry.category }}
          <Text class="tabular-nums">{{ scheduleEntry.number }}</Text>
          {{ scheduleEntry.title }}
        </Text>
        <TocEntryPageNumber
          v-if="config.options.pageNumbers"
          :id="`${id}-${period.id}-${scheduleEntry.id}`"
        />
      </Link>
    </template>
  </template>
</template>
<script>
import PdfComponent from '@/pdf/PdfComponent.js'
import { filterMatchScheduleEntry } from '@/common/helpers/filterMatchScheduleEntry.js'
import { dateLong } from '@/common/helpers/dateHelperUTCFormatted.js'
import TocEntryPageNumber from '../TocEntryPageNumber.vue'
import sortBy from 'lodash-es/sortBy.js'

export default {
  name: 'Program',
  components: { TocEntryPageNumber },
  extends: PdfComponent,
  props: {
    period: { type: Object, required: true },
    filter: { type: Object, default: () => ({}) },
    config: { type: Object, required: true },
  },
  computed: {
    anyScheduleEntries() {
      return this.period.scheduleEntries().items.length
    },
    scheduleEntries() {
      return this.period
        .scheduleEntries()
        .items.filter((scheduleEntry) => {
          return filterMatchScheduleEntry(scheduleEntry, this.filter)
        })
        .map((scheduleEntry) => {
          const activity = scheduleEntry.activity()
          return {
            ...scheduleEntry,
            category: activity.category().short,
            title: activity.title,
          }
        })
    },
    days() {
      return sortBy(this.period.days().items, (day) => this.$date.utc(day.start).unix())
        .map((day) => ({
          day,
          scheduleEntries: this.scheduleEntries.filter(
            (scheduleEntry) => scheduleEntry.day()._meta.self === day._meta.self
          ),
        }))
        .filter(({ scheduleEntries }) => scheduleEntries.length)
    },
  },
  methods: {
    date(day) {
      return dateLong(day.start, this.$tc)
    },
  },
}
</script>
<style lang="react-pdf">
.toc-day-entry {
  margin-top: 2pt;
}
.toc-activity-entry {
  padding-left: 28pt;
}
</style>

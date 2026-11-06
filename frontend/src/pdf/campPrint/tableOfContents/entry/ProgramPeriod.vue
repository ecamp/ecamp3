<template>
  <template v-if="anyScheduleEntries">
    <Link class="toc-entry" :href="`#${id}-${period.id}`">
      <Text>{{ $tc('print.program.title') }}: {{ period.description }}</Text>
      <TocEntryPageNumber v-if="config.options.pageNumbers" :id="`${id}-${period.id}`" />
    </Link>
    <View>
      <template v-for="{ day, scheduleEntries: dayScheduleEntries } in days">
        <View class="toc-entry toc-day-entry">
          <Text>{{ $tc('entity.day.name') }} {{ day.number }}</Text>
          <View v-if="!config.options.pageNumbers" class="toc-day-entry-line" />
          <Text class="toc-day-entry-date">{{ date(day) }}</Text>
        </View>
        <Link
          v-for="scheduleEntry in dayScheduleEntries"
          class="toc-entry toc-sub-entry toc-activity-entry"
          :class="{ 'toc-activity-entry-indented': config.options.pageNumbers }"
          :href="`#${id}-${period.id}-${scheduleEntry.id}`"
          :wrap="false"
        >
          <View class="toc-activity-entry-text">
            <Text class="tabular-nums">{{ scheduleEntry.number }}</Text>
            <CategoryLabel :line-font-size="10" :category="scheduleEntry.category" />
            {{ scheduleEntry.title }}
          </View>
          <TocEntryPageNumber
            v-if="config.options.pageNumbers"
            :id="`${id}-${period.id}-${scheduleEntry.id}`"
          />
        </Link>
      </template>
    </View>
  </template>
</template>
<script>
import PdfComponent from '@/pdf/PdfComponent.js'
import { filterMatchScheduleEntry } from '@/helpers/filterMatchScheduleEntry.js'
import { dateLong } from '@/helpers/dateHelperUTCFormatted.js'
import TocEntryPageNumber from '../TocEntryPageNumber.vue'
import sortBy from 'lodash-es/sortBy.js'
import CategoryLabel from '@/pdf/campPrint/CategoryLabel.vue'

export default {
  name: 'Program',
  components: { CategoryLabel, TocEntryPageNumber },
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
            category: activity.category(),
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
  margin-bottom: 4pt;
  flex-direction: row;
  justify-content: space-between;
  font-size: 10pt;
}
.toc-day-entry-line {
  flex-grow: 1;
  align-self: center;
  border-top: 0.5pt solid #aaaaaa;
  margin: 0 6pt;
}
.toc-day-entry-date {
  opacity: 0.7;
  font-weight: 400;
}
.toc-activity-entry {
  padding-left: 0;
  font-size: 10pt;
}
.toc-activity-entry-indented {
  padding-left: 19pt;
}
.toc-activity-entry-text {
  flex-direction: row;
  gap: 3pt;
}
</style>

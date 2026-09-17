<template>
  <View class="program-day-summary">
    <View class="program-day-title">
      <Text>{{ $tc('entity.day.name') }} {{ day.number }}</Text>
      <Text>{{ date }}</Text>
    </View>
    <View v-if="dayResponsibles" class="program-day-responsibles">
      <Text class="program-day-responsibles-title"
        >{{ $tc('entity.day.fields.dayResponsibles') }}:
      </Text>
      <Text>{{ dayResponsibles }}</Text>
    </View>
    <View class="program-day-table">
      <View class="program-day-table-row program-day-table-header" :wrap="false">
        <Text class="program-day-table-time">
          {{ $tc('entity.scheduleEntry.fields.time') }}
        </Text>
        <Text class="program-day-table-number">
          {{ $tc('entity.scheduleEntry.fields.nr') }}
        </Text>
        <Text class="program-day-table-activity">
          {{ $tc('entity.activity.fields.title') }}
        </Text>
        <Text class="program-day-table-responsibles">
          {{ $tc('entity.activity.fields.responsible') }}
        </Text>
      </View>
      <View
        v-for="scheduleEntry in scheduleEntries"
        class="program-day-table-row"
        :wrap="false"
      >
        <Text class="program-day-table-time">
          {{ rangeTime(scheduleEntry) }}
        </Text>
        <Text class="program-day-table-number">{{ scheduleEntry.number }}</Text>
        <Link
          class="program-day-table-activity"
          :href="`#scheduleEntry_${scheduleEntry.id}`"
        >
          <CategoryLabel
            :category="scheduleEntry.activity().category()"
            class="program-day-category"
          />
          <Text class="program-day-activity-title">
            {{ scheduleEntry.activity().title }}
          </Text>
        </Link>
        <View class="program-day-table-responsibles">
          <Responsibles :activity="scheduleEntry.activity()" />
        </View>
      </View>
    </View>
  </View>
</template>
<script>
import PdfComponent from '@/pdf/PdfComponent.js'
import CategoryLabel from '../CategoryLabel.vue'
import Responsibles from '../Responsibles.vue'
import { dayResponsiblesCommaSeparated } from '../../../common/helpers/dayResponsibles.js'

export default {
  name: 'DaySummary',
  components: { CategoryLabel, Responsibles },
  extends: PdfComponent,
  props: {
    day: { type: Object, required: true },
    scheduleEntries: { type: Array, required: true },
  },
  computed: {
    date() {
      return this.$date.utc(this.day.start).format(this.$tc('global.datetime.dateLong'))
    },
    dayResponsibles() {
      return dayResponsiblesCommaSeparated(this.day, this.$tc)
    },
  },
  methods: {
    rangeTime(scheduleEntry) {
      const start = this.$date.utc(scheduleEntry.start)
      const end = this.$date.utc(scheduleEntry.end)

      if (start.isSame(end, 'day')) {
        return `${start.format('HH:mm')} - ${end.format('HH:mm')}`
      }

      const dayLabel = this.$tc('entity.day.name')
      return `${dayLabel}\u00a0${this.dayNumber(start)}:\u00a0${start.format(
        'HH:mm'
      )} - ${dayLabel}\u00a0${this.dayNumber(end)}:\u00a0${end.format('HH:mm')}`
    },
    dayNumber(date) {
      const dayOffset = date
        .startOf('day')
        .diff(this.$date.utc(this.day.start).startOf('day'), 'day')
      return Number(this.day.number) + dayOffset
    },
  },
}
</script>
<style lang="react-pdf">
.program-day-summary {
  margin-bottom: 12pt;
  border-bottom: 2pt solid black;
}
.program-day-title {
  display: flex;
  flex-direction: row;
  justify-content: space-between;
  background-color: black;
  color: white;
  font-size: 15pt;
  font-weight: 600;
  margin-top: 4pt;
  padding: 5pt;
}
.program-day-responsibles {
  font-size: 10pt;
  margin-top: 6pt;
  display: flex;
  flex-direction: row;
}
.program-day-responsibles-title {
  font-weight: 600;
}
.program-day-table {
  margin-top: 8pt;
  font-size: 10pt;
  break-inside: avoid;
}
.program-day-table-row {
  display: flex;
  flex-direction: row;
  align-items: flex-start;
  border-bottom: 0.5pt solid #d1d5db;
  padding: 3pt 0;
}
.program-day-table-header {
  border-bottom: 0.5pt solid black;
  font-weight: 600;
}
.program-day-table-time {
  width: 80pt;
  flex-shrink: 0;
  font-feature-settings: 'tnum';
}
.program-day-table-number {
  width: 30pt;
  flex-shrink: 0;
}
.program-day-table-activity {
  display: flex;
  flex-direction: row;
  align-items: start;
  flex-basis: 0;
  flex-grow: 1;
  flex-shrink: 0;
  padding-right: 5pt;
  text-decoration: none;
  color: unset;
}
.program-day-category {
  font-size: 7pt;
  margin-right: 4pt;
  margin-bottom: auto;
}
.program-day-activity-title {
  flex-grow: 1;
  flex-shrink: 0;
  max-width: 32vw;
}
.program-day-table-responsibles {
  flex-shrink: 0;
  flex-grow: 1;
  max-width: 20vw;
}
</style>

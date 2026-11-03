<template>
  <Page
    :size="config.options.pageSize || 'A4'"
    :orientation="orientation"
    class="page"
    :class="{ 'page-compact': compact }"
  >
    <slot></slot>
    <TocSectionStartMarker :id="`${id}-${period.id}`" />
    <View class="picasso-header">
      <YSLogo
        v-if="period.camp().printYSLogoOnPicasso"
        :size="22"
        :locale="config.language"
        class="picasso-ys-logo"
      />
      <TimeColumnSpacer v-else :times="times.slice(0, times.length - 1)" />
      <View class="picasso-title-container">
        <Text :id="`${id}-${period.id}`" :bookmark="bookmark" class="picasso-title">
          {{ $tc('print.picasso.title', { period: period.description }) }}
        </Text>
        <Text class="picasso-organizer">{{ period.camp().organizer }}</Text>
      </View>
      <TimeColumnSpacer
        v-if="!compact && pageSize === 'A3'"
        :times="times.slice(0, times.length - 1)"
      />
    </View>
    <View class="picasso-calendar-header-container">
      <TimeColumnSpacer :times="times.slice(0, times.length - 1)" />
      <DayHeader
        v-for="day in days"
        :day="day"
        :show-day-responsibles="anyDayResponsibles"
        class="picasso-day-header"
        :class="{
          'picasso-day-header-compact': compact,
          'picasso-day-header-left-border': day.id === days.at(-1).id,
        }"
      />
      <TimeColumnSpacer
        v-if="!compact && pageSize === 'A3'"
        :times="times.slice(0, times.length - 1)"
      />
    </View>
    <View class="picasso-calendar-container">
      <TimeColumn :times="times.slice(0, times.length - 1)" align="left" />
      <DayColumn
        v-for="day in days"
        :times="times"
        :day="day"
        :schedule-entries="scheduleEntries"
        :avatars="content.options.responsibles === 'avatars'"
        :config="config"
        :show-y-s-physical-activity-duration="
          content.options.showYSPhysicalActivityDuration === true
        "
      />
      <TimeColumn
        v-if="!compact && pageSize === 'A3'"
        :times="times.slice(0, times.length - 1)"
        align="right"
      />
    </View>
    <View class="picasso-legend">
      <TimeColumnSpacer :times="times.slice(0, times.length - 1)" />
      <View style="flex-basis: 0; flex-grow: 1; gap: 6pt; padding-top: 4pt; height: auto">
        <Categories :period="period" :schedule-entries="scheduleEntries" />
        <PicassoFooter :period="period" :locale="config.locale" />
      </View>
      <TimeColumnSpacer
        v-if="!compact && pageSize === 'A3'"
        :times="times.slice(0, times.length - 1)"
      />
    </View>
  </Page>
</template>
<script>
import PdfComponent from '@/pdf/PdfComponent.js'
import YSLogo from '../YSLogo.vue'
import TimeColumnSpacer from './TimeColumnSpacer.vue'
import DayHeader from './DayHeader.vue'
import TimeColumn from './TimeColumn.vue'
import DayColumn from './DayColumn.vue'
import Categories from './Categories.vue'
import PicassoFooter from './PicassoFooter.vue'
import { filterDayResponsiblesByDay } from '@/helpers/dayResponsibles.js'
import { times } from '@/helpers/picasso.js'
import { filterMatchScheduleEntry } from '@/helpers/filterMatchScheduleEntry.js'
import TocSectionStartMarker from '../TocSectionStartMarker.vue'

export default {
  name: 'PicassoPage',
  components: {
    TocSectionStartMarker,
    YSLogo,
    TimeColumnSpacer,
    DayHeader,
    TimeColumn,
    DayColumn,
    Categories,
    PicassoFooter,
  },
  extends: PdfComponent,
  props: {
    config: { type: Object, required: true },
    content: { type: Object, required: true },
    period: { type: Object, required: true },
    days: { type: Object, required: true },
    bedtime: { type: Number, default: 0 },
    getUpTime: { type: Number, default: 24 },
    timeStep: { type: Number, default: 1 },
  },
  computed: {
    bookmark() {
      return {
        title: this.$tc('print.picasso.title', {
          period: this.period.description,
        }),
        fit: true,
      }
    },
    times() {
      return times(this.getUpTime, this.bedtime, this.timeStep)
    },
    orientation() {
      return this.content.options.orientation === 'L' ? 'landscape' : 'portrait'
    },
    anyDayResponsibles() {
      return this.days.some((day) => filterDayResponsiblesByDay(day).length > 0)
    },
    scheduleEntries() {
      return this.period.scheduleEntries().items.filter((scheduleEntry) => {
        return filterMatchScheduleEntry(scheduleEntry, this.content.options.filter)
      })
    },
  },
}
</script>
<style lang="react-pdf">
.picasso-header {
  display: flex;
  flex-grow: 0;
  flex-direction: row;
  margin-top: -4pt;
  margin-bottom: 4pt;
  align-items: center;
}
.picasso-title-container {
  display: flex;
  flex-direction: row;
  flex-wrap: wrap;
  align-items: baseline;
  flex-grow: 1;
  gap: 8pt;
}
.picasso-title {
  flex-grow: 1;
  font-weight: 600;
  font-size: 14pt;
}
.picasso-organizer {
  font-size: 10pt;
}
.picasso-ys-logo {
  align-self: flex-end;
  margin-top: 3pt;
  margin-right: 8pt;
}
.picasso-calendar-header-container {
  display: flex;
  flex-grow: 0;
  flex-direction: row;
}
.picasso-calendar-container {
  flex-grow: 1;
  flex-shrink: 1;
  flex-basis: 0;
  display: flex;
  flex-direction: row;
}
.picasso-day-header {
  background-color: #cfd8dc;
  margin-right: 1pt;
  flex-basis: 0;
  flex-grow: 1;
  padding: 4pt 0;
  display: flex;
  align-items: center;
  text-align: center;
  flex-direction: column;
}
.picasso-day-header-compact {
  padding: 2pt 0;
}
.picasso-day-header-left-border {
  margin-right: 0;
}
.picasso-legend {
  flex-grow: 0;
  display: flex;
  flex-direction: row;
}
.picasso-time-column {
  flex-grow: 0;
  flex-shrink: 0;
  display: flex;
  flex-direction: column;
}
.picasso-time-column-text {
  margin-top: 0.2pt;
  font-size: 8pt;
}
</style>

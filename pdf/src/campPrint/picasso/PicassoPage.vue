<template>
  <Page size="A4" :orientation="orientation" class="page">
    <View class="picasso-title-container">
      <YSLogo
        v-if="period.camp().printYSLogoOnPicasso"
        :size="20"
        :locale="config.language"
        class="picasso-ys-logo"
      />
      <Text :id="`${id}-${period.id}`" :bookmark="bookmark" class="picasso-title">
        {{ $tc('print.picasso.title', { period: period.description }) }}
      </Text>
      <Text class="picasso-organizer">{{ period.camp().organizer }}</Text>
    </View>
    <View class="picasso-calendar-header-container">
      <TimeColumnSpacer :times="times.slice(0, times.length - 1)" />
      <DayHeader
        v-for="day in days"
        :day="day"
        :show-day-responsibles="anyDayResponsibles"
        class="picasso-day-header"
        :class="{ 'picasso-day-header-left-border': day.id === days[0].id }"
      />
      <TimeColumnSpacer :times="times.slice(0, times.length - 1)" />
    </View>
    <View class="picasso-calendar-container">
      <TimeColumn :times="times.slice(0, times.length - 1)" align="right" />
      <DayColumn
        v-for="day in days"
        :times="times"
        :day="day"
        :schedule-entries="scheduleEntries"
        :class="{ 'picasso-day-column-left-border': day.id === days[0].id }"
      />
      <TimeColumn :times="times.slice(0, times.length - 1)" align="left" />
    </View>
    <Categories :period="period" />
    <PicassoFooter :period="period" :locale="config.locale" />
  </Page>
</template>
<script>
import PdfComponent from '@/PdfComponent.js'
import YSLogo from '../YSLogo.vue'
import TimeColumnSpacer from './TimeColumnSpacer.vue'
import DayHeader from './DayHeader.vue'
import TimeColumn from './TimeColumn.vue'
import DayColumn from './DayColumn.vue'
import Categories from './Categories.vue'
import PicassoFooter from './PicassoFooter.vue'
import { filterDayResponsiblesByDay } from '../../../common/helpers/dayResponsibles.js'
import { times } from '../../../common/helpers/picasso.js'

export default {
  name: 'PicassoPage',
  components: {
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
      return this.period.scheduleEntries().items
    },
  },
}
</script>
<pdf-style>
.picasso-title-container {
  display: flex;
  flex-direction: row;
  margin-top: -4pt;
  margin-bottom: 4pt;
  align-items: center;
  gap: 8pt;
}
.picasso-title {
  flex-grow: 1;
  font-weight: bold;
  font-size: 14pt;
}
.picasso-organizer {
  font-size: 10pt;
}
.picasso-ys-logo {
  align-self: flex-end;
  margin-top: 3pt;
  size: 20;
}
.picasso-calendar-header-container {
  border-left: 1pt solid white;
  border-right: 1pt solid white;
  flex-grow: 1;
  display: flex;
  flex-direction: row;
  line-height: 1;
}
.picasso-calendar-container {
  border: 1pt solid grey;
  flex-grow: 1;
  display: flex;
  flex-direction: row;
  line-height: 1;
}
.picasso-day-header {
  border-right: 1pt solid white;
  flex-basis: 0;
  flex-grow: 1;
  overflow: hidden;
  padding: 4pt 0 5pt;
  display: flex;
  flex-direction: column;
}
.picasso-day-header-left-border {
  border-left: 1pt solid white;
}
.picasso-time-column {
  flex-grow: 0;
  flex-shrink: 0;
  display: flex;
  flex-direction: column;
}
.picasso-time-column-text {
  font-size: 8pt;
}
.picasso-day-column-left-border {
  border-left: 1pt solid grey;
}
</pdf-style>

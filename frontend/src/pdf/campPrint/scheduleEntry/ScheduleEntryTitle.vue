<template>
  <TocSectionStartMarker :id="id" />
  <View :wrap="false" :min-presence-ahead="75">
    <View
      class="schedule-entry-header-title"
      :class="{ 'schedule-entry-header-title-compact': compact }"
      :style="{ borderBottomColor: activity.category().color }"
    >
      <View :id="`scheduleEntry_${scheduleEntry.id}`" class="schedule-entry-title">
        <CategoryLabel
          :category="activity.category()"
          :size="15"
          :line-font-size="18"
          class="schedule-entry-category-label"
        />
        <Text :id="id" :bookmark="bookmarkTitle" class="schedule-entry-number-and-title">
          <Text class="tabular-nums">{{ scheduleEntry.number }}</Text>
          {{ activity.title }}
        </Text>
      </View>
      <View class="schedule-entry-date">
        <Text>{{ startAt }}&nbsp;</Text>
        <Text>-</Text>
        <Text>&nbsp;{{ endAt }}</Text>
      </View>
    </View>
    <View v-if="showHeaderRow" class="schedule-entry-header">
      <View class="schedule-entry-header-metadata">
        <View class="schedule-entry-header-metadata-entry"
          ><Text class="schedule-entry-header-metadata-label"
            >{{ $tc('entity.activity.fields.location') }}:</Text
          ><Text>{{ activity.location }}</Text></View
        >
      </View>
      <View class="schedule-entry-header-divider" />
      <View class="schedule-entry-header-metadata">
        <View class="schedule-entry-header-metadata-entry">
          <Text class="schedule-entry-header-metadata-label"
            >{{ $tc('entity.activity.fields.responsible') }}:</Text
          >
          <Responsibles :activity="activity" style="max-width: 200pt" />
        </View>
      </View>
    </View>
  </View>
</template>
<script>
import PdfComponent from '@/pdf/PdfComponent.js'
import CategoryLabel from '../CategoryLabel.vue'
import Responsibles from '../Responsibles.vue'
import TocSectionStartMarker from '../TocSectionStartMarker.vue'
import { dateShort, hourShort } from '../../../common/helpers/dateHelperUTCFormatted.js'

export default {
  name: 'ScheduleEntryTitle',
  components: { TocSectionStartMarker, Responsibles, CategoryLabel },
  extends: PdfComponent,
  props: {
    scheduleEntry: { type: Object, required: true },
    showHeader: { type: Boolean, required: false, default: true },
  },
  computed: {
    activity() {
      return this.scheduleEntry.activity()
    },
    bookmarkTitle() {
      return [
        this.activity.category().short,
        this.scheduleEntry.number,
        this.activity.title,
      ]
        .filter((entry) => entry)
        .join(' ')
    },
    startAt() {
      const start = this.scheduleEntry.start
      return `${dateShort(start, this.$tc)} ${hourShort(start, this.$tc)}`
    },
    endAt() {
      const { start, end } = this.scheduleEntry
      if (dateShort(start, this.$tc) === dateShort(end, this.$tc)) {
        return hourShort(end, this.$tc)
      }
      return `${dateShort(end, this.$tc)} ${hourShort(end, this.$tc)}`
    },
    showHeaderRow() {
      if (!this.showHeader) return false
      if (!this.compact) return true
      return (
        this.activity.location.length > 0 ||
        this.activity.activityResponsibles().items.length > 0
      )
    },
  },
}
</script>
<style lang="react-pdf">
.schedule-entry-header-title {
  display: flex;
  flex-direction: row;
  justify-content: space-between;
  align-items: flex-start;
  padding-bottom: 1pt;
  border-bottom: 2pt solid #aaaaaa;
}
.schedule-entry-header-title-compact {
  padding-bottom: 0;
}
.schedule-entry-title {
  flex-basis: 0;
  flex-grow: 1;
  display: flex;
  flex-direction: row;
  gap: 4pt;
  padding: 4pt 0;
  font-size: 18pt;
  font-weight: 600;
}
.schedule-entry-category-label {
  flex-shrink: 0;
}
.schedule-entry-number-and-title {
  flex-shrink: 1;
}
.schedule-entry-date {
  font-size: 11pt;
  margin-top: 10.78pt;
  display: flex;
  flex-grow: 0;
  flex-shrink: 1;
  flex-direction: row;
  justify-content: flex-end;
  flex-wrap: wrap;
  max-width: 33vw;
}
.schedule-entry-header {
  display: flex;
  flex-direction: row;
  justify-content: space-between;
  border-bottom: 0.5pt solid black;
  font-size: 10pt;
  margin-bottom: 10pt;
}
.schedule-entry-header-divider {
  border-left: 0.5pt solid black;
  margin-left: 3.5pt;
  padding-left: 5pt
}
.schedule-entry-header-metadata {
  width: 50%;
  padding: 2pt 0;
}
.schedule-entry-header-metadata-entry {
  flex-direction: row;
  align-items: flex-start;
  column-gap: 6pt;
}
.schedule-entry-header-metadata-label {
  font-weight: 600;
  flex-shrink: 0;
  flex-grow: 0;
}
</style>

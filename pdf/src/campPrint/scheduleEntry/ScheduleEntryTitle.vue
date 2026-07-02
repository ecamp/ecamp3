<template>
  <TocSectionStartMarker :id="id" />
  <View :wrap="false" :min-presence-ahead="75">
    <View
      class="schedule-entry-header-title"
      :style="{ borderBottomColor: activity.category().color }"
    >
      <View :id="`scheduleEntry_${scheduleEntry.id}`" class="schedule-entry-title">
        <CategoryLabel
          :category="activity.category()"
          class="schedule-entry-category-label"
        />
        <Text :id="id" :bookmark="bookmarkTitle" class="schedule-entry-number-and-title">
          {{ scheduleEntry.number }} {{ activity.title }}
        </Text>
      </View>
      <View class="schedule-entry-date">
        <Text>{{ startAt }}&nbsp;</Text>
        <Text>-</Text>
        <Text>&nbsp;{{ endAt }}</Text>
      </View>
    </View>
    <View v-if="showHeaderData" class="schedule-entry-header">
      <View class="schedule-entry-header-metadata">
        <View class="schedule-entry-header-metadata-entry"
          ><Text v-if="activity.location" class="schedule-entry-header-metadata-label"
            >{{ $tc('entity.activity.fields.location') }}:</Text
          ><Text>{{ activity.location }}</Text></View
        >
      </View>
      <View class="schedule-entry-header-divider" />
      <View class="schedule-entry-header-metadata">
        <View class="schedule-entry-header-metadata-entry">
          <Text
            v-if="activity.activityResponsibles().items.length"
            class="schedule-entry-header-metadata-label"
            >{{ $tc('entity.activity.fields.responsible') }}:</Text
          >
          <Responsibles :activity="activity" style="max-width: 200pt" />
        </View>
      </View>
    </View>
  </View>
</template>
<script>
import PdfComponent from '@/PdfComponent.js'
import CategoryLabel from '../CategoryLabel.vue'
import Responsibles from '../Responsibles.vue'
import TocSectionStartMarker from '../TocSectionStartMarker.vue'

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
    start() {
      return this.$date.utc(this.scheduleEntry.start)
    },
    end() {
      return this.$date.utc(this.scheduleEntry.end)
    },
    startAt() {
      return this.start.format('ddd l LT')
    },
    endAt() {
      return this.start.format('ddd l') === this.end.format('ddd l')
        ? this.end.format('LT')
        : this.end.format('ddd l LT')
    },
    showHeaderData() {
      return (
        (this.activity.location.length ||
          this.activity.activityResponsibles().items.length) &&
        this.showHeader
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
  align-items: center;
  padding-bottom: 2pt;
  border-bottom: 2pt solid #aaaaaa;
}
.schedule-entry-title {
  flex-basis: 0;
  flex-grow: 1;
  display: flex;
  flex-direction: row;
  font-size: 14pt;
  font-weight: 600;
}
.schedule-entry-category-label {
  margin: 4pt 0;
  font-size: 12pt;
  flex-shrink: 0;
}
.schedule-entry-number-and-title {
  margin: 4pt 4pt;
  flex-shrink: 1;
}
.schedule-entry-date {
  font-size: 11pt;
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

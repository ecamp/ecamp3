<template>
  <View :wrap="false" :min-presence-ahead="75">
    <View class="schedule-entry-header-title">
      <View class="h1 schedule-entry-title">
        <Text
          :id="id"
          :bookmark="{ title: bookmarkTitle, fit: true }"
          style="margin: 4pt 0"
        >
          {{ scheduleEntry.number }}
        </Text>
        <CategoryLabel :category="activity.category()" style="margin: 4pt 3pt" />
        <Text style="margin: 4pt 0">{{ activity.title }}</Text>
      </View>
      <View class="schedule-entry-date">
        <View class="schedule-entry-date-text">
          <Text>{{ startAt }}</Text>
        </View>
        <View class="schedule-entry-date-text">
          <Text>- {{ endAt }}</Text>
        </View>
      </View>
    </View>
    <View class="schedule-entry-header">
      <View class="schedule-entry-header-left">
        <Text class="schedule-entry-header-metadata-label">{{
          $tc('entity.activity.fields.location')
        }}</Text>
        <Text class="schedule-entry-header-metadata-label">{{
          $tc('entity.activity.fields.responsible')
        }}</Text>
      </View>
      <View class="schedule-entry-header-right">
        <Text class="schedule-entry-header-metadata-value">{{ activity.location }}</Text>
        <Responsibles class="schedule-entry-header-metadata-value" :activity="activity" />
      </View>
    </View>
  </View>
  <View style="margin-bottom: 20pt">
    <ContentNode :content-node="activity.rootContentNode()" />
  </View>
</template>
<script>
import PdfComponent from '@/PdfComponent.js'
import CategoryLabel from '../CategoryLabel.vue'
import Responsibles from '../picasso/Responsibles.vue'
import ContentNode from './contentNode/ContentNode.vue'
import { setContentNodeComponent } from './contentNode/ColumnLayout.vue'

setContentNodeComponent(ContentNode)

export default {
  name: 'ScheduleEntry',
  components: { Responsibles, CategoryLabel, ContentNode },
  extends: PdfComponent,
  props: {
    scheduleEntry: { type: Object, required: true },
  },
  computed: {
    activity() {
      return this.scheduleEntry.activity()
    },
    bookmarkTitle() {
      return (
        this.activity.category().short +
        ' ' +
        this.scheduleEntry.number +
        ' ' +
        this.activity.title
      )
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
  },
}
</script>
<pdf-style>
.schedule-entry-header-title {
  display: flex;
  flex-direction: row;
  border-bottom: 1px solid black;
  padding-bottom: 8pt;
}
.schedule-entry-title {
  flex-grow: 1;
  display: flex;
  flex-direction: row;
}
.schedule-entry-date {
  display: flex;
  flex-direction: column;
  justify-content: center;
}
.schedule-entry-date-text {
  display: flex;
  flex-direction: row;
  justify-content: flex-end;
}
.schedule-entry-header {
  display: flex;
  flex-direction: row;
}
.schedule-entry-header-left {
  display: flex;
  flex-direction: column;
  flex-shrink: 0;
  border-right: 1px solid black;
}
.schedule-entry-header-right {
  display: flex;
  flex-direction: column;
  flex-grow: 1;
}
.schedule-entry-header-metadata-label {
  height: 16pt;
  border-bottom: 1px solid black;
  padding: 2pt 4pt 2pt 0;
}
.schedule-entry-header-metadata-value {
  height: 16pt;
  border-bottom: 1px solid black;
  overflow: ellipsis;
  padding: 2pt 0 2pt 4pt;
}
</pdf-style>

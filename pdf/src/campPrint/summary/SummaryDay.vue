<template>
  <View :id="`${id}-${period.id}-${day.id}`" class="summary-day-title-container">
    <Text class="summary-day-title">{{ $tc('entity.day.name') }} {{ day.number }}</Text>
    <Text class="summary-day-date">{{ date }}</Text>
  </View>
  <template v-for="{ scheduleEntry, contentNodes } in entriesWithContentNodes">
    <template v-for="chapter in contentNodes">
      <View class="summary-chapter-title" :min-presence-ahead="30">
        <CategoryLabel
          :category="scheduleEntry.activity().category()"
          style="font-size: 10pt"
        />
        <Text :id="`${id}-${period.id}-${scheduleEntry.id}`" style="margin: 0 3pt">
          {{ scheduleEntry.number }} {{ chapter.title }}
        </Text>
      </View>
      <View style="line-height: 1.6">
        <RichText :rich-text="chapter.data.html" />
      </View>
    </template>
  </template>
</template>
<script>
import PdfComponent from '@/PdfComponent.js'
import { dateLong } from '../../../common/helpers/dateHelperUTCFormatted.js'
import CategoryLabel from '../CategoryLabel.vue'
import RichText from '../RichText.vue'
import { isEmptyHtml } from '../helpers.js'
import camelCase from 'lodash/camelCase.js'

export default {
  name: 'SummaryDay',
  components: { RichText, CategoryLabel },
  extends: PdfComponent,
  props: {
    period: { type: Object, required: true },
    day: { type: Object, required: true },
    contentType: { type: String, required: true },
    instanceNameFilter: { type: String, default: '' },
  },
  computed: {
    date() {
      return dateLong(this.day.start, this.$tc)
    },
    scheduleEntries() {
      return this.period.scheduleEntries().items.filter((scheduleEntry) => {
        return scheduleEntry.day()._meta.self === this.day._meta.self
      })
    },
    entries() {
      return this.scheduleEntries.map((scheduleEntry) => ({
        scheduleEntry,
        contentNodes: this.period
          .contentNodes()
          .items.filter(
            (contentNode) =>
              contentNode.contentTypeName === this.contentType &&
              contentNode.root()._meta.self ===
                scheduleEntry.activity().rootContentNode()._meta.self &&
              !isEmptyHtml(contentNode.data.html) &&
              (!this.instanceNameFilter ||
                contentNode.instanceName
                  ?.toLowerCase()
                  .includes(this.instanceNameFilter.toLowerCase()) ||
                this.$tc(`contentNode.${camelCase(this.contentType)}.name`)
                  .toLowerCase()
                  .includes(this.instanceNameFilter.toLowerCase()))
          )
          .map((chapter) => ({
            ...chapter,
            title: this.chapterTitle(chapter, scheduleEntry),
          })),
      }))
    },
    entriesWithContentNodes() {
      return this.entries.filter(({ contentNodes }) => contentNodes.length)
    },
  },
  methods: {
    chapterTitle(chapter, scheduleEntry) {
      return (
        scheduleEntry.activity().title +
        (chapter.instanceName ? ' - ' + chapter.instanceName : '')
      )
    },
  },
}
</script>
<pdf-style>
.summary-day-title-container {
  display: flex;
  flex-direction: row;
  justify-content: space-between;
  align-items: baseline;
  border-bottom: 2pt solid #aaaaaa;
  padding-bottom: 2pt;
  margin-bottom: 1pt;
}
.summary-day-title {
  font-size: 14;
  font-weight: semibold;
  margin: 10pt 0 3pt;
}
.summary-day-date {
  font-size: 11pt;
}
.summary-chapter-title {
  display: flex;
  flex-direction: row;
  align-items: center;
  font-weight: bold;
  margin: 10pt 0 4.5pt;
}
</pdf-style>

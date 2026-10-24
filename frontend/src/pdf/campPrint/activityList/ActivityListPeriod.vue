<template>
  <Text
    :id="`${id}-${period.id}`"
    :bookmark="{ title: title + ': ' + period.description, fit: true }"
    class="activity-list-period-title"
    >{{ title }}: {{ period.description }}</Text
  >
  <ActivityListScheduleEntry
    v-for="scheduleEntry in scheduleEntries"
    :id="id"
    :schedule-entry="scheduleEntry"
    :content-types="contentTypes"
    :content-nodes="contentNodes"
  />
</template>
<script>
import PdfComponent from '@/pdf/PdfComponent.js'
import ActivityListScheduleEntry from './ActivityListScheduleEntry.vue'
import camelCase from 'lodash-es/camelCase.js'
import { filterMatchScheduleEntry } from '@/common/helpers/filterMatchScheduleEntry.js'

export default {
  name: 'ActivityListPeriod',
  components: { ActivityListScheduleEntry },
  extends: PdfComponent,
  props: {
    period: { type: Object, required: true },
    contentTypeNames: { type: Array, required: true },
    config: { type: Object, required: true },
    filter: { type: Object, default: () => ({}) },
  },
  computed: {
    scheduleEntries() {
      return this.period
        .scheduleEntries()
        .items.filter((scheduleEntry) =>
          filterMatchScheduleEntry(scheduleEntry, this.filter)
        )
    },
    allContentTypes() {
      return this.api.get('/content_types').items
    },
    contentTypes() {
      return this.contentTypeNames.map((contentTypeName) =>
        this.allContentTypes.find((contentType) => contentType.name === contentTypeName)
      )
    },
    contentNodes() {
      return this.contentTypes.map((contentType) =>
        this.period.contentNodes().items.filter((contentNode) => {
          return contentNode.contentType()._meta.self === contentType._meta.self
        })
      )
    },
    title() {
      return this.$tc('print.activityList.title')
    },
  },
  methods: { camelCase },
}
</script>
<style lang="react-pdf">
.activity-list-period-title {
  font-size: 10pt;
  font-weight: bold;
  text-align: center;
}
</style>

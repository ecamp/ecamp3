<template>
  <Page :id="id" class="page activity-page" :size="config.options.pageSize || 'A4'">
    <slot></slot>
    <TocSectionStartMarker :id="`${id}-${scheduleEntry.id}`" />
    <ScheduleEntryContents
      :id="`${id}-${scheduleEntry.id}`"
      :schedule-entry="scheduleEntry"
    />
  </Page>
</template>
<script>
import PdfComponent from '@/pdf/PdfComponent.js'
import ScheduleEntryContents from '../scheduleEntry/ScheduleEntryContents.vue'
import TocSectionStartMarker from '../TocSectionStartMarker.vue'

export default {
  name: 'Activity',
  components: { TocSectionStartMarker, ScheduleEntryContents },
  extends: PdfComponent,
  props: {
    content: { type: Object, required: true },
    config: { type: Object, required: true },
  },
  computed: {
    scheduleEntry() {
      return this.api.get(this.content.options.scheduleEntry)
    },
  },
}
</script>
<style lang="react-pdf">
.activity-page {
  font-size: 8pt;
}
</style>

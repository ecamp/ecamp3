<template>
  <Page :id="id" class="page activity-page">
    <ScheduleEntry :id="`${id}-${scheduleEntry.id}`" :schedule-entry="scheduleEntry" />
  </Page>
</template>
<script>
import PdfComponent from '@/PdfComponent.js'
import ScheduleEntry from '../scheduleEntry/ScheduleEntry.vue'

export default {
  name: 'Activity',
  components: { ScheduleEntry },
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
<pdf-style>
.activity-page {
  font-size: 8pt;
}
</pdf-style>

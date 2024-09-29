<template>
  <ScheduleEntryTitle :schedule-entry="scheduleEntry" />
  <View style="padding-bottom: 20pt; font-size: 10pt">
    <ContentNode :content-node="activity.rootContentNode()" />
  </View>
</template>
<script>
import PdfComponent from '@/PdfComponent.js'
import ScheduleEntryTitle from './ScheduleEntryTitle.vue'
import ContentNode from './contentNode/ContentNode.vue'
import { setContentNodeComponent as setContentNodeComponentColumn } from './contentNode/ColumnLayout.vue'
import { setContentNodeComponent as setContentNodeComponentDefault } from './contentNode/ResponsiveLayout.vue'

setContentNodeComponentColumn(ContentNode)
setContentNodeComponentDefault(ContentNode)

export default {
  name: 'ScheduleEntry',
  components: { ScheduleEntryTitle, ContentNode },
  extends: PdfComponent,
  props: {
    scheduleEntry: { type: Object, required: true },
  },
  computed: {
    activity() {
      return this.scheduleEntry.activity()
    },
  },
}
</script>
<pdf-style>
</pdf-style>

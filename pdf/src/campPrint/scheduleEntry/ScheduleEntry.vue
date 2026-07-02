<template>
  <ScheduleEntryTitle :id="id" :schedule-entry="scheduleEntry" />
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
  provide() {
    return {
      camp: this.camp,
    }
  },
  props: {
    scheduleEntry: { type: Object, required: true },
  },
  computed: {
    activity() {
      return this.scheduleEntry.activity()
    },
    camp() {
      return this.activity.camp()
    },
  },
}
</script>
<style lang="react-pdf"></style>

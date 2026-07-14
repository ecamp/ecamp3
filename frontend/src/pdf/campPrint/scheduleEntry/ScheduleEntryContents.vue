<template>
  <ScheduleEntryTitle :id="id" :schedule-entry="scheduleEntry" />
  <View style="padding-bottom: 20pt; font-size: 10pt">
    <ContentNodeRenderer :content-node="activity.rootContentNode()" />
  </View>
</template>
<script>
import PdfComponent from '@/pdf/PdfComponent.js'
import ScheduleEntryTitle from './ScheduleEntryTitle.vue'
import ContentNodeRenderer from './contentNode/ContentNodeRenderer.vue'
import { setContentNodeComponent as setContentNodeComponentColumn } from './contentNode/ColumnLayout.vue'
import { setContentNodeComponent as setContentNodeComponentDefault } from './contentNode/ResponsiveLayout.vue'

setContentNodeComponentColumn(ContentNodeRenderer)
setContentNodeComponentDefault(ContentNodeRenderer)

export default {
  name: 'ScheduleEntryContents',
  components: { ScheduleEntryTitle, ContentNodeRenderer },
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

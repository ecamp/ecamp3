<template>
  <ScheduleEntryTitle :schedule-entry="scheduleEntry" :show-header="false" />

  <View style="margin-top: 10pt; padding-bottom: 20pt; font-size: 10pt">
    <ContentNode
      v-for="contentNode in contentNodeEntries"
      :key="contentNode.id"
      :content-node="contentNode"
    />
  </View>
</template>
<script>
import PdfComponent from '@/PdfComponent.js'
import ScheduleEntryTitle from '../scheduleEntry/ScheduleEntryTitle.vue'
import ContentNode from '../scheduleEntry/contentNode/ContentNode.vue'
import sortBy from 'lodash/sortBy.js'

export default {
  name: 'ScheduleEntry',
  components: { ContentNode, ScheduleEntryTitle },
  extends: PdfComponent,
  props: {
    scheduleEntry: { type: Object, required: true },
    contentTypes: { type: Array, required: true },
    contentNodes: { type: Array, required: true },
  },
  computed: {
    contentNodeEntries() {
      return sortBy(
        this.contentNodes.map((contentNodeList) =>
          contentNodeList.filter(
            (contentNode) =>
              contentNode.root()._meta.self ===
              this.scheduleEntry.activity().rootContentNode()._meta.self
          )
        ),
        ['parent', 'slot', 'position']
      ).flat()
    },
  },
}
</script>
<pdf-style>
</pdf-style>

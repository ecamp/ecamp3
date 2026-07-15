<template>
  <ScheduleEntryTitle
    :id="`${id}-${scheduleEntry.id}`"
    :schedule-entry="scheduleEntry"
    :show-header="false"
  />

  <View style="margin-top: 10pt; padding-bottom: 20pt; font-size: 10pt">
    <ContentNodeRenderer
      v-for="contentNode in contentNodeEntries"
      :key="contentNode.id"
      :content-node="contentNode"
    />
  </View>
</template>
<script>
import PdfComponent from '@/pdf/PdfComponent.js'
import ScheduleEntryTitle from '../scheduleEntry/ScheduleEntryTitle.vue'
import ContentNodeRenderer from '../scheduleEntry/contentNode/ContentNodeRenderer.vue'
import sortBy from 'lodash-es/sortBy.js'

export default {
  name: 'ActivityListScheduleEntry',
  components: { ContentNodeRenderer, ScheduleEntryTitle },
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
<style lang="react-pdf"></style>

<template>
  <Link v-if="scheduleEntry" class="toc-entry" :href="`#${id}-${scheduleEntry.id}`">
    <View class="toc-activity-single">
      <Text class="tabular-nums">{{ scheduleEntry.number }}</Text>
      <CategoryLabel :line-font-size="12" :category="scheduleEntry.category" />
      <Text>{{ scheduleEntry.title }}</Text>
    </View>
    <TocEntryPageNumber
      v-if="config.options.pageNumbers"
      :id="`${id}-${scheduleEntry.id}`"
    />
  </Link>
</template>
<script>
import PdfComponent from '@/pdf/PdfComponent.js'
import TocEntryPageNumber from '../TocEntryPageNumber.vue'
import CategoryLabel from '@/pdf/campPrint/CategoryLabel.vue'

export default {
  name: 'Activity',
  components: { CategoryLabel, TocEntryPageNumber },
  extends: PdfComponent,
  props: {
    entry: { type: Object, required: true },
    config: { type: Object, required: true },
  },
  computed: {
    scheduleEntry() {
      if (!this.entry.options.scheduleEntry) return null

      const scheduleEntry = this.api.get(this.entry.options.scheduleEntry)
      const activity = scheduleEntry.activity()
      return {
        ...scheduleEntry,
        category: activity.category(),
        title: activity.title,
      }
    },
  },
}
</script>
<style lang="react-pdf">
.toc-activity-single {
  display: flex;
  flex-direction: row;
  gap: 2pt;
}
</style>

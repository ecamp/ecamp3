<template>
  <Link v-if="scheduleEntry" class="toc-entry" :href="`#${id}-${scheduleEntry.id}`">
    <Text>
      {{ scheduleEntry.category }}
      {{ scheduleEntry.number }}
      {{ scheduleEntry.title }}
    </Text>
    <TocEntryPageNumber
      v-if="config.options.pageNumbers"
      :id="`${id}-${scheduleEntry.id}`"
    />
  </Link>
</template>
<script>
import PdfComponent from '@/PdfComponent.js'
import TocEntryPageNumber from '../TocEntryPageNumber.vue'

export default {
  name: 'Activity',
  components: { TocEntryPageNumber },
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
        category: activity.category().short,
        title: activity.title,
      }
    },
  },
}
</script>
<style lang="react-pdf"></style>

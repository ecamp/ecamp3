<template>
  <template v-if="anyScheduleEntries">
    <Link class="toc-entry" :href="`#${id}-${period.id}`">
      <Text>{{ period.description }}</Text>
      <TocEntryPageNumber v-if="config.options.pageNumbers" :id="`${id}-${period.id}`" />
    </Link>
    <Link
      v-for="scheduleEntry in scheduleEntries"
      class="toc-entry toc-sub-entry"
      :href="`#${id}-${period.id}-${scheduleEntry.id}`"
    >
      <Text>
        {{ scheduleEntry.category }}
        {{ scheduleEntry.number }}
        {{ scheduleEntry.title }}
      </Text>
      <TocEntryPageNumber
        v-if="config.options.pageNumbers"
        :id="`${id}-${period.id}-${scheduleEntry.id}`"
      />
    </Link>
  </template>
</template>
<script>
import PdfComponent from '@/PdfComponent.js'
import { filterMatchScheduleEntry } from '@/../common/helpers/filterMatchScheduleEntry.js'
import TocEntryPageNumber from '../TocEntryPageNumber.vue'

export default {
  name: 'Program',
  components: { TocEntryPageNumber },
  extends: PdfComponent,
  props: {
    period: { type: Object, required: true },
    filter: { type: Object, default: () => ({}) },
    config: { type: Object, required: true },
  },
  computed: {
    anyScheduleEntries() {
      return this.period.scheduleEntries().items.length
    },
    scheduleEntries() {
      return this.period
        .scheduleEntries()
        .items.filter((scheduleEntry) => {
          return filterMatchScheduleEntry(scheduleEntry, this.filter)
        })
        .map((scheduleEntry) => {
          const activity = scheduleEntry.activity()
          return {
            ...scheduleEntry,
            category: activity.category().short,
            title: activity.title,
          }
        })
    },
  },
}
</script>
<style lang="react-pdf"></style>

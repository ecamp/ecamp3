<template>
  <template v-if="anyScheduleEntries">
    <Link class="toc-entry" :href="`#${id}-${period.id}`">
      <Text>{{ period.description }}</Text>
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
    </Link>
  </template>
</template>
<script>
import PdfComponent from '@/PdfComponent.js'
import sortBy from 'lodash/sortBy.js'

export default {
  name: 'Program',
  extends: PdfComponent,
  props: {
    period: { type: Object, required: true },
  },
  computed: {
    anyScheduleEntries() {
      return this.period.scheduleEntries().items.length
    },
    scheduleEntries() {
      return sortBy(this.period.scheduleEntries().items, [
        'dayNumber',
        'scheduleEntryNumber',
      ]).map((scheduleEntry) => {
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
<pdf-style>
</pdf-style>

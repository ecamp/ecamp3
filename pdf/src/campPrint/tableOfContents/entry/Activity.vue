<template>
  <Link v-if="scheduleEntry" class="toc-entry" :href="`#${id}-${scheduleEntry.id}`">
    <Text>
      {{ scheduleEntry.category }}
      {{ scheduleEntry.number }}
      {{ scheduleEntry.title }}
    </Text>
  </Link>
</template>
<script>
import PdfComponent from '@/PdfComponent.js'

export default {
  name: 'Activity',
  extends: PdfComponent,
  props: {
    entry: { type: Object, required: true },
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
<pdf-style>
</pdf-style>

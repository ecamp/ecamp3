<template>
  <Link v-for="period in periods" class="toc-entry" :href="`#${id}-${period.id}`">
    <Text>{{ $tc('print.story.title') }}: {{ period.description }}</Text>
    <TocEntryPageNumber v-if="config.options.pageNumbers" :id="`${id}-${period.id}`" />
  </Link>
</template>
<script>
import Summary from './Summary.vue'
import TocEntryPageNumber from '../TocEntryPageNumber.vue'

export default {
  name: 'Story',
  components: { TocEntryPageNumber },
  extends: Summary,
}
</script>

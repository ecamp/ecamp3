<template>
  <Link v-for="period in periods" class="toc-entry" :href="`#${id}-${period.id}`">
    <Text>{{ $tc('print.summary.storycontext.title') }}: {{ period.description }}</Text>
  </Link>
</template>
<script>
import Summary from './Summary.vue'

export default {
  name: 'Story',
  extends: Summary,
}
</script>

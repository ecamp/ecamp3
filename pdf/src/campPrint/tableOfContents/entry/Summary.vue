<template>
  <Link v-for="period in periods" class="toc-entry" :href="`#${id}-${period.id}`">
    <Text
      >{{ $tc('print.summary.title') }}
      {{ $tc(`contentNode.${camelCase(entry.options.contentType)}.name`)
      }}<template v-if="entry.options.filter"> "{{ entry.options.filter }}"</template>:
      {{ period.description }}</Text
    >
  </Link>
</template>
<script>
import PdfComponent from '@/PdfComponent.js'
import camelCase from 'lodash/camelCase.js'

export default {
  name: 'Summary',
  methods: { camelCase },
  extends: PdfComponent,
  props: {
    entry: { type: Object, required: true },
  },
  computed: {
    periods() {
      return this.entry.options.periods.map((periodUri) => this.api.get(periodUri))
    },
  },
}
</script>
<pdf-style>
</pdf-style>

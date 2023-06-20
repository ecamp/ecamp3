<template>
  <Page size="A4" class="page">
    <Text :id="id" :bookmark="$tc('print.toc.title')" class="h1">{{
      $tc('print.toc.title')
    }}</Text>
    <template v-for="(entry, index) in config.contents">
      <component
        :is="entryComponents[entry.type]"
        v-if="entry.type in entryComponents"
        :id="`entry-${index}`"
        :key="index"
        :entry="entry"
      ></component>
      <Text v-else :key="`unknown-${index}`">{{ entry.type }}</Text>
    </template>
  </Page>
</template>
<script>
import PdfComponent from '@/PdfComponent.js'
import Cover from './entry/Cover.vue'
import Toc from './entry/Toc.vue'
import Picasso from './entry/Picasso.vue'
import Program from './entry/Program.vue'
import Activity from './entry/Activity.vue'
import Story from './entry/Story.vue'

export default {
  name: 'Cover',
  extends: PdfComponent,
  props: {
    content: { type: Object, required: true },
    config: { type: Object, required: true },
  },
  computed: {
    entryComponents() {
      return {
        Cover,
        Toc,
        Picasso,
        Program,
        Activity,
        Story,
      }
    },
  },
}
</script>
<pdf-style>
.toc-entry {
  display: flex;
  flex-direction: row;
  justify-content: space-between;
}
.toc-sub-entry {
  margin-left: 10pt;
}
</pdf-style>

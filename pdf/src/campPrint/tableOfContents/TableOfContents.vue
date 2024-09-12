<template>
  <Page size="A4" class="page">
    <Text :id="id" :bookmark="$tc('print.toc.title')" class="toc-title">{{
      $tc('print.toc.title')
    }}</Text>
    <View style="line-height: 1.6">
      <template v-for="(entry, index) in config.contents">
        <component
          :is="entryComponents[entry.type]"
          v-if="entry.type in entryComponents"
          :id="`entry-${index}`"
          :entry="entry"
        ></component>
        <Text v-else>{{ entry.type }}</Text>
      </template>
    </View>
  </Page>
</template>
<script>
import PdfComponent from '@/PdfComponent.js'
import Cover from './entry/Cover.vue'
import Toc from './entry/Toc.vue'
import Picasso from './entry/Picasso.vue'
import Program from './entry/Program.vue'
import Activity from './entry/Activity.vue'
import SafetyConsiderations from './entry/SafetyConsiderations.vue'
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
        SafetyConsiderations,
        Story,
      }
    },
  },
}
</script>
<pdf-style>
.toc-title {
  font-weight: semibold;
  font-size: 14pt;
  border-bottom: 2pt solid #aaaaaa;
  padding-bottom: 2pt;
  margin-bottom: 8pt;
}
.toc-entry {
  display: flex;
  flex-direction: row;
  justify-content: space-between;
  color: black;
  text-decoration: none;
}
.toc-sub-entry {
  margin-left: 10pt;
}
</pdf-style>

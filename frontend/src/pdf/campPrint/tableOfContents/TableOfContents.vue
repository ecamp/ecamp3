<template>
  <Page :size="config.options.pageSize || 'A4'" class="page">
    <slot></slot>
    <TocSectionStartMarker :id="id" />
    <Text :id="id" :bookmark="$tc('print.toc.title')" class="toc-title">{{
      $tc('print.toc.title')
    }}</Text>
    <View>
      <template v-for="(entry, index) in config.contents">
        <component
          :is="entryComponents[entry.type]"
          v-if="entry.type in entryComponents"
          :id="`entry-${index}`"
          :entry="entry"
          :config="config"
        ></component>
        <Text v-else>{{ entry.type }}</Text>
      </template>
    </View>
  </Page>
</template>
<script>
import PdfComponent from '@/pdf/PdfComponent.js'
import Cover from './entry/Cover.vue'
import Toc from './entry/Toc.vue'
import Picasso from './entry/Picasso.vue'
import Program from './entry/Program.vue'
import Activity from './entry/Activity.vue'
import SafetyConsiderations from './entry/SafetyConsiderations.vue'
import Story from './entry/Story.vue'
import ActivityList from './entry/ActivityList.vue'
import TocSectionStartMarker from '../TocSectionStartMarker.vue'

export default {
  name: 'Cover',
  components: { TocSectionStartMarker },
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
        ActivityList,
      }
    },
  },
}
</script>
<style lang="react-pdf">
.toc-title {
  font-weight: 600;
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
  width: 100%;
  margin-bottom: 6pt;
}
.toc-sub-entry {
  padding-left: 10pt;
  margin-bottom: 4pt;
}
</style>

<template>
  <View class="content-node storyboard-container">
    <InstanceName :content-node="contentNode" :icon="mdiScriptTextOutline" />
    <View class="storyboard-header-row">
      <View class="storyboard-header-cell storyboard-column-1" :class="headerClass">
        <Text>{{ $tc('contentNode.storyboard.entity.section.fields.column1') }}</Text>
      </View>
      <View class="storyboard-header-cell storyboard-column-2" :class="headerClass">
        <Text>{{ $tc('contentNode.storyboard.entity.section.fields.column2Html') }}</Text>
      </View>
      <View
        class="storyboard-header-cell storyboard-column-3"
        :class="[headerClass, column3Class]"
      >
        <Text>{{ $tc('contentNode.storyboard.entity.section.fields.column3') }}</Text>
      </View>
    </View>
    <View v-for="section in sections" class="storyboard-row">
      <View class="storyboard-cell storyboard-column-1" :class="cellClass">
        <Text>{{ section.column1 }}</Text>
      </View>
      <View class="storyboard-cell storyboard-column-2" :class="cellClass">
        <RichText :rich-text="section.column2Html" />
      </View>
      <View
        class="storyboard-cell storyboard-column-3"
        :class="[cellClass, column3Class]"
      >
        <Text>{{ section.column3 }}</Text>
      </View>
    </View>
  </View>
</template>
<script>
import PdfComponent from '@/pdf/PdfComponent.js'
import InstanceName from '../InstanceName.vue'
import { mdiScriptTextOutline } from '@mdi/js'
import RichText from '../../RichText.vue'
import sortBy from 'lodash-es/sortBy.js'
import { isEmptyHtml } from '../../helpers.js'

function sectionHasContent(section) {
  return (
    section.column1.length > 0 ||
    !isEmptyHtml(section.column2Html) ||
    section.column3.length > 0
  )
}

export default {
  name: 'Storyboard',
  components: { InstanceName, RichText },
  extends: PdfComponent,
  isEmpty(contentNode) {
    return !Object.values(contentNode.data.sections).some(sectionHasContent)
  },
  props: {
    contentNode: { type: Object, required: true },
  },
  data() {
    return { mdiScriptTextOutline }
  },
  computed: {
    headerClass() {
      return { 'storyboard-header-cell-compact': this.compact }
    },
    cellClass() {
      return { 'storyboard-cell-compact': this.compact || this.pageSize === 'A5' }
    },
    column3Class() {
      return { 'storyboard-column-3-wide': this.pageSize !== 'A5' }
    },
    sections() {
      const sections = this.contentNode.data.sections
      return sortBy(
        Object.keys(sections).map((key) => ({
          key,
          column1: sections[key].column1,
          column2Html: sections[key].column2Html,
          column3: sections[key].column3,
          position: sections[key].position,
        })),
        (section) => section.position
      )
    },
  },
}
</script>
<style lang="react-pdf">
.storyboard-container {
  display: flex;
  flex-direction: column;
}
.storyboard-header-row {
  display: flex;
  flex-direction: row;
  border-bottom: 0.75pt solid #94A3B8;
}
.storyboard-row {
  display: flex;
  flex-direction: row;
}
.storyboard-header-cell {
  line-height: 0.8;
  font-weight: 600;
  padding-top: 2pt;
  padding-bottom: 2pt;
}
.storyboard-header-cell-compact {
  padding-top: 0;
  padding-bottom: 0;
}
.storyboard-cell {
  line-height: 0.8;
  padding-top: 2pt;
  padding-bottom: 6pt;
}
.storyboard-cell-compact {
  padding-top: 1pt;
  padding-bottom: 3pt;
}
.storyboard-column-1 {
  font-feature-settings: 'tnum';
  width: 42pt;
  flex-shrink: 0;
  flex-grow: 0;
  padding-right: 2pt;
  overflow: hidden;
}
.storyboard-column-2 {
  flex-basis: 0;
  flex-grow: 1;
  border-left: 0.75pt solid #94A3B8;
  padding-horizontal: 2pt;
}
.storyboard-column-3 {
  flex-basis: 40pt;
  flex-shrink: 0;
  flex-grow: 0;
  border-left: 0.75pt solid #94A3B8;
  padding-left: 2pt;
  overflow: hidden;
}
.storyboard-column-3-wide {
  flex-basis: 70pt;
}
</style>

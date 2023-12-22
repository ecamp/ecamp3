<template>
  <View class="content-node storyboard-container">
    <InstanceName :content-node="contentNode" />
    <View v-if="anyContent" class="storyboard-header-row">
      <View class="storyboard-header-cell storyboard-column-1">
        <Text>{{ $tc('contentNode.storyboard.entity.section.fields.column1') }}</Text>
      </View>
      <View class="storyboard-header-cell storyboard-column-2">
        <Text>{{ $tc('contentNode.storyboard.entity.section.fields.column2Html') }}</Text>
      </View>
      <View class="storyboard-header-cell storyboard-column-3">
        <Text>{{ $tc('contentNode.storyboard.entity.section.fields.column3') }}</Text>
      </View>
    </View>
    <View v-for="section in sections" class="storyboard-row">
      <View class="storyboard-cell storyboard-column-1">
        <Text>{{ section.column1 }}</Text>
      </View>
      <View class="storyboard-cell storyboard-column-2">
        <RichText :rich-text="section.column2Html" />
      </View>
      <View class="storyboard-cell storyboard-column-3">
        <Text>{{ section.column3 }}</Text>
      </View>
    </View>
  </View>
</template>
<script>
import PdfComponent from '@/PdfComponent.js'
import InstanceName from '../InstanceName.vue'
import RichText from '../../RichText.vue'
import sortBy from 'lodash/sortBy.js'
import { isEmptyHtml } from '../../helpers.js'

export default {
  name: 'Storyboard',
  components: { InstanceName, RichText },
  extends: PdfComponent,
  props: {
    contentNode: { type: Object, required: true },
  },
  computed: {
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
    anyContent() {
      return (
        this.sections.length &&
        (this.sections[0].column1.length ||
          !isEmptyHtml(this.sections[0].column2Html) ||
          this.sections[0].column3.length)
      )
    },
  },
}
</script>
<pdf-style>
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
  line-height: 1.4;
  font-weight: semibold;
}
.storyboard-cell {
  line-height: 1.5;
  padding-top: 1pt;
  padding-bottom: 3pt;
}
.storyboard-column-1 {
  width: 28pt;
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
</pdf-style>

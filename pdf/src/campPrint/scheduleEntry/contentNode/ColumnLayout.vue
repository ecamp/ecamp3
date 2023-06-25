<template>
  <View v-if="columns.length" class="column-layout-container">
    <View v-for="{ slot, width } in columns" :style="columnStyle(slot, width)">
      <template v-for="child in children[slot]">
        <component :is="contentNodeComponent" :content-node="child" />
      </template>
    </View>
  </View>
</template>
<script>
import PdfComponent from '@/PdfComponent.js'
import groupBy from 'lodash/groupBy.js'
import sortBy from 'lodash/sortBy.js'

// Workaround for circular component imports: ContentNode needs ColumnLayout, and ColumnLayout needs ContentNode.
// The default way to do this in Vue 3 would be using defineAsyncComponent. But that requires a dynamic import,
// which resolves the imported component too late for our one-time non-reactive renderer.
// Instead, we fix this using a dependency injection pattern: Pass the dependency in from a place outside the
// circular import structure.
let contentNodeComponent
export function setContentNodeComponent(component) {
  contentNodeComponent = component
}

export default {
  name: 'ColumnLayout',
  extends: PdfComponent,
  props: {
    contentNode: { type: Object, required: true },
  },
  computed: {
    columns() {
      return this.contentNode.data.columns
    },
    firstSlot() {
      return this.columns.length ? this.columns[0].slot : '1'
    },
    lastSlot() {
      return this.columns.length ? this.columns[this.columns.length - 1].slot : '1'
    },
    children() {
      return groupBy(
        sortBy(this.contentNode.children().items, (child) => parseInt(child.position)),
        (child) => child.slot
      )
    },
    contentNodeComponent() {
      return contentNodeComponent
    },
  },
  methods: {
    columnStyle(slot, width) {
      return {
        borderLeft: slot === this.firstSlot ? 'none' : '1px solid black',
        padding:
          '2pt ' +
          (slot === this.lastSlot ? '0' : '1%') +
          ' 2pt ' +
          (slot === this.firstSlot ? '0' : '1%'),
        flexBasis: width * 1000 + 'pt',
      }
    },
  },
}
</script>
<pdf-style>
.column-layout-container {
  display: flex;
  flex-direction: row;
}
</pdf-style>

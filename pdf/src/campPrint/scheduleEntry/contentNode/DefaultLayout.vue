<template>
  <View v-if="hasChildren" class="default-layout__container">
    <View class="default-layout__flex">
      <View
        v-for="child in children['aside-top']"
        :key="child.id"
        class="default-layout__flex_item"
      >
        <component :is="contentNodeComponent" :content-node="child" />
      </View>
    </View>
    <View>
      <template v-for="child in children['main']" :key="child.id">
        <component :is="contentNodeComponent" :content-node="child" />
      </template>
    </View>
    <View class="default-layout__flex">
      <View
        v-for="child in children['aside-bottom']"
        :key="child.id"
        class="default-layout__flex_item"
      >
        <component :is="contentNodeComponent" :content-node="child" />
      </View>
    </View>
  </View>
</template>

<script>
import PdfComponent from '@/PdfComponent.js'
import groupBy from 'lodash/groupBy.js'
import sortBy from 'lodash/sortBy.js'

// Workaround for circular component imports: ContentNode needs DefaultLayout, and DefaultLayout needs ContentNode.
// The default way to do this in Vue 3 would be using defineAsyncComponent. But that requires a dynamic import,
// which resolves the imported component too late for our one-time non-reactive renderer.
// Instead, we fix this using a dependency injection pattern: Pass the dependency in from a place outside the
// circular import structure.
let contentNodeComponent
export function setContentNodeComponent(component) {
  contentNodeComponent = component
}

export default {
  name: 'DefaultLayout',
  extends: PdfComponent,
  props: {
    contentNode: { type: Object, required: true },
  },
  computed: {
    hasChildren() {
      return this.contentNode.children().items.length > 0
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
}
</script>
<pdf-style>
.default-layout__container {
  display: flex;
  flex-direction: column;
}
.default-layout__flex {
  display: flex;
  flex-direction: row;
  flex-wrap: wrap;
  overflow: hidden;
  margin-left: -10pt;
  margin-right: -10pt;
}
.default-layout__flex_item {
  flex-grow: 1;
  flex-basis: 200pt;
  background-color: #fff;
  border-left: 0.75pt solid black;
  margin-left: -1pt;
  padding-left: 9pt;
  padding-right: 10pt;
}
</pdf-style>

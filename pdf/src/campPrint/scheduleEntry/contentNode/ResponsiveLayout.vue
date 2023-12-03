<template>
  <View v-if="hasChildren" class="responsive-layout__container">
    <View class="responsive-layout__flex">
      <View
        v-for="child in children['aside-top']"
        :key="child.id"
        class="responsive-layout__flex_item"
      >
        <component :is="contentNodeComponent" :content-node="child" />
      </View>
    </View>
    <View>
      <template v-for="child in children['main']" :key="child.id">
        <component :is="contentNodeComponent" :content-node="child" />
      </template>
    </View>
    <View class="responsive-layout__flex">
      <View
        v-for="child in children['aside-bottom']"
        :key="child.id"
        class="responsive-layout__flex_item"
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

// Workaround for circular component imports: ContentNode needs ResponsiveLayout, and ResponsiveLayout needs ContentNode.
// The default way to do this in Vue 3 would be using defineAsyncComponent. But that requires a dynamic import,
// which resolves the imported component too late for our one-time non-reactive renderer.
// Instead, we fix this using a dependency injection pattern: Pass the dependency in from a place outside the
// circular import structure.
let contentNodeComponent
export function setContentNodeComponent(component) {
  contentNodeComponent = component
}

export default {
  name: 'ResponsiveLayout',
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
.responsive-layout__container {
  display: flex;
  flex-direction: column;
}
.responsive-layout__flex {
  display: flex;
  flex-direction: row;
  flex-wrap: wrap;
  overflow: hidden;
  margin-left: -10pt;
  margin-right: -10pt;
}
.responsive-layout__flex_item {
  flex-grow: 1;
  flex-basis: 200pt;
  background-color: #fff;
  border-left: 0.75pt solid black;
  margin-left: -1pt;
  padding-left: 9pt;
  padding-right: 10pt;
}
</pdf-style>

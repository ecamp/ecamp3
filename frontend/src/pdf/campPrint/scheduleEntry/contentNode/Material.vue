<template>
  <View class="content-node material" :class="{ 'content-node-compact': compact }">
    <InstanceName :content-node="contentNode" :icon="mdiPackageVariant" />
    <View v-for="item in sortedMaterialItems" class="material-item">
      <Text class="material-item-quantity" :style="{ width: quantityWidth }">{{
        item.quantity
      }}</Text>
      <Text class="material-item-unit" :style="{ width: unitWidth }">{{
        unit(item)
      }}</Text>
      <Text class="material-item-article">{{ item.article }}</Text>
      <Text class="material-item-list">{{
        item.materialList ? item.materialList()?.name : ''
      }}</Text>
    </View>
  </View>
</template>
<script>
import PdfComponent from '@/pdf/PdfComponent.js'
import InstanceName from '../InstanceName.vue'
import { mdiPackageVariant } from '@mdi/js'
import sortBy from 'lodash-es/sortBy.js'
import { Font } from '@/pdf/renderer/index.js'

export default {
  name: 'Material',
  components: { InstanceName },
  extends: PdfComponent,
  isEmpty(contentNode) {
    return contentNode.materialItems().items.length === 0
  },
  props: {
    contentNode: { type: Object, required: true },
  },
  data() {
    return { mdiPackageVariant }
  },
  computed: {
    quantityWidth() {
      return this.columnWidth(this.sortedMaterialItems.map(({ quantity }) => quantity))
    },
    unitWidth() {
      return this.columnWidth(this.sortedMaterialItems.map(this.unit))
    },
    sortedMaterialItems() {
      return sortBy(this.contentNode.materialItems().items, (item) =>
        item.materialList ? item.materialList()?.name : ''
      )
    },
  },
  methods: {
    columnWidth(values) {
      const font = Font.getFont({ fontFamily: 'InterDisplay' }).data
      return values.reduce((width, value) => {
        const { positions } = font.layout(String(value ?? ''), { tnum: 1 })
        const advance = positions.reduce((sum, position) => sum + position.xAdvance, 0)
        return Math.max(width, (advance * 10) / font.unitsPerEm)
      }, 0)
    },
    unit(item) {
      if (item.unit) return item.unit
      return item.quantity ? '×' : ''
    },
  },
}
</script>
<style lang="react-pdf">
.material {
  display: flex;
  flex-direction: column;
}
.material-item {
  display: flex;
  flex-direction: row;
  gap: 3pt;
  font-feature-settings: 'tnum';
  margin-bottom: 4pt;
}
.material-item-quantity {
  flex-shrink: 0;
  text-align: right;
}
.material-item-unit {
  flex-shrink: 0;
}
.material-item-article {
  flex-basis: 0;
  flex-grow: 65;
}
.material-item-list {
  flex-basis: 0;
  flex-grow: 30;
}
</style>

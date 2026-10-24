<template>
  <View class="content-node material">
    <InstanceName :content-node="contentNode" :icon="mdiPackageVariant" />
    <View v-for="item in sortedMaterialItems" class="material-item">
      <Text class="material-item-quantity">{{ item.quantity }}</Text>
      <Text class="material-item-unit">{{ unit(item) }}</Text>
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

export default {
  name: 'Material',
  components: { InstanceName },
  extends: PdfComponent,
  props: {
    contentNode: { type: Object, required: true },
  },
  data() {
    return { mdiPackageVariant }
  },
  computed: {
    sortedMaterialItems() {
      return sortBy(this.contentNode.materialItems().items, (item) =>
        item.materialList ? item.materialList()?.name : ''
      )
    },
  },
  methods: {
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
}
.material-item-quantity {
  width: 20pt;
  flex-shrink: 0;
  text-align: right;
}
.material-item-unit {
  width: 28pt;
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

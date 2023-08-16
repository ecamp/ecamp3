<template>
  <View class="content-node material">
    <InstanceName :content-node="contentNode" />
    <View v-for="item in sortedMaterialItems" class="material-item">
      <View class="material-item-column material-item-column-1">
        <Text>{{ item.quantity }} {{ item.unit }} {{ item.article }}</Text>
      </View>
      <View class="material-item-column material-item-column-2">
        <Text>{{ item.materialList().name }}</Text>
      </View>
    </View>
  </View>
</template>
<script>
import PdfComponent from '@/PdfComponent.js'
import InstanceName from '../InstanceName.vue'
import sortBy from 'lodash/sortBy.js'

export default {
  name: 'Material',
  components: { InstanceName },
  extends: PdfComponent,
  props: {
    contentNode: { type: Object, required: true },
  },
  computed: {
    sortedMaterialItems() {
      return sortBy(
        this.contentNode.materialItems().items,
        (item) => item.materialList().name
      )
    },
  },
}
</script>
<pdf-style>
.material {
  display: flex;
  flex-direction: column;
}
.material-item {
  display: flex;
  flex-direction: row;
}
.material-item-column {
  flex-grow: 1;
  border-bottom: 0.3pt solid black;
}
.material-item-column-1 {
  flex-basis: 7000pt;
  padding-right: 2pt;
}
.material-item-column-2 {
  flex-basis: 3000pt;
  padding-left: 2pt;
}
</pdf-style>

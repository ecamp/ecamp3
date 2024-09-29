<template>
  <View class="content-node-title">
    <Text class="content-node-instance-name">{{ instanceName }}</Text>
    <Text v-if="contentNode.instanceName" class="content-type-name">{{
      contentTypeName
    }}</Text>
  </View>
</template>
<script>
import PdfComponent from '@/PdfComponent.js'
import camelCase from 'lodash/camelCase.js'

export default {
  name: 'InstanceName',
  extends: PdfComponent,
  props: {
    contentNode: { type: Object, required: true },
  },
  computed: {
    instanceName() {
      return this.contentNode.instanceName || this.contentTypeName
    },
    contentTypeName() {
      return this.$tc(`contentNode.${camelCase(this.contentNode.contentTypeName)}.name`)
    },
  },
}
</script>
<pdf-style>
.content-node-title {
  border-bottom: 1.5pt solid black;
  margin-bottom: 1pt;
  display: flex;
  flex-direction: row;
  justify-content: space-between;
  align-items: baseline;
}
.content-node-instance-name {
  flex-grow: 1;
  font-weight: bold;
  font-size: 11pt;
  padding-bottom: 3pt;
}
.content-type-name {
  font-size:8pt;
  font-weight:normal;
  color:grey;
}
</pdf-style>

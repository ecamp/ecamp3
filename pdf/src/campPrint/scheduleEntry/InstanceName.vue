<template>
  <Text class="content-node-instance-name">{{ instanceName }}</Text>
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
      return (
        this.contentNode.instanceName ||
        this.$tc(`contentNode.${camelCase(this.contentNode.contentTypeName)}.name`)
      )
    },
  },
}
</script>
<pdf-style>
.content-node-instance-name {
  font-weight: bold;
  font-size: 11pt;
  padding-bottom: 3pt;
  border-bottom: 1.5pt solid black;
  margin-bottom: 1pt;
}
</pdf-style>

<template>
  <View class="content-node-title" :class="{ 'content-node-title-compact': compact }">
    <View class="center-wrapper">
      <Text>&zwj;</Text>
      <Svg v-if="icon" class="content-node-icon" view-box="0 0 24 24">
        <Path :d="icon" fill="#757575" />
      </Svg>
    </View>
    <View class="content-node-title__content">
      <Text class="content-node-instance-name">{{ instanceName }}</Text>
      <Text v-if="contentNode.instanceName" class="content-type-name">{{
        contentTypeName
      }}</Text>
    </View>
  </View>
</template>
<script>
import PdfComponent from '@/pdf/PdfComponent.js'
import camelCase from 'lodash-es/camelCase.js'

export default {
  name: 'InstanceName',
  extends: PdfComponent,
  props: {
    contentNode: { type: Object, required: true },
    icon: { type: String, default: null },
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
<style lang="react-pdf">
.content-node-title {
  border-bottom: 1.5pt solid black;
  margin-bottom: 6pt;
  padding: 0 0 4pt;
  display: flex;
  flex-direction: row;
  align-items: baseline;
  gap: 6pt;
}
.content-node-title-compact {
  margin-bottom: 3pt;
  padding: 0 0 2pt;
}
.content-node-title__content {
  display: flex;
  flex-wrap: wrap;
  flex-direction: row;
  align-items: baseline;
  flex-grow: 1;
  justify-content: space-between;
}
.center-wrapper {
  display: flex;
  align-items: center;
  flex-direction: row;
  font-size: 11pt;
}
.content-node-icon {
  width: 11pt;
  height: 11pt;
  margin-bottom: 1pt;
}
.content-node-instance-name {
  flex-grow: 1;
  font-weight: 600;
  font-size: 11pt;
}
.content-type-name {
  font-size: 8pt;
  font-style: italic;
  color: grey;
}
</style>

<!-- 
component to render ContentNode with actual content (non-layout nodes)
-->
<template>
  <div class="tw-mb-3">
    <h3 class="tw-text-lg">
      <v-icon v-if="iconPath" :size="16">{{ iconPath }}</v-icon>
      {{ instanceOrContentTypeName }}
    </h3>
    <slot />
  </div>
</template>

<script>
import camelCase from 'lodash/camelCase'

export default {
  props: {
    contentNode: { type: Object, required: true },
    iconPath: { type: String, required: false, default: null },
  },
  computed: {
    instanceOrContentTypeName() {
      return (
        this.contentNode.instanceName ||
        this.$tc(`contentNode.${camelCase(this.contentNode.contentTypeName)}.name`)
      )
    },
  },
}
</script>

<!-- 
component to render ContentNode with actual content (non-layout nodes)
-->
<template>
  <div class="tw-mb-3">
    <h3 class="tw-text-lg">
      <span :class="'mdi ' + icon" />
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
  },
  computed: {
    instanceOrContentTypeName() {
      return (
        this.contentNode.instanceName ||
        this.$tc(`contentNode.${camelCase(this.contentNode.contentTypeName)}.name`)
      )
    },
    icon() {
      return this.$tc(`contentNode.${camelCase(this.contentNode.contentTypeName)}.icon`)
    },
  },
}
</script>

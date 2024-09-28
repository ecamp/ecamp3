<!--
component to render ContentNode with actual content (non-layout nodes)
-->
<template>
  <div class="tw-mb-3">
    <h3
      class="tw-text-lg tw-break-after-avoid tw-font-semibold tw-border-b-black tw-border-b-2 tw-flex tw-items-center tw-py-1 tw-mb-2 tw-gap-2"
    >
      <svg-icon v-if="iconPath" type="mdi" class="icon" :size="16" :path="iconPath" />
      {{ instanceOrContentTypeName }}
      <span
        v-if="contentNode.instanceName"
        class="tw-text-sm tw-ml-auto tw-text-slate-400 tw-italic tw-font-normal"
        >{{ contentTypeName }}</span
      >
    </h3>
    <div class="tw-min-h-[50px]">
      <slot />
    </div>
  </div>
</template>

<script>
import camelCase from 'lodash/camelCase'
import SvgIcon from '@jamescoyle/vue-icon'

export default {
  components: { SvgIcon },
  props: {
    contentNode: { type: Object, required: true },
    iconPath: { type: String, required: false, default: null },
  },
  computed: {
    instanceOrContentTypeName() {
      return this.contentNode.instanceName || this.contentTypeName
    },
    contentTypeName() {
      return this.$t(`contentNode.${camelCase(this.contentNode.contentTypeName)}.name`)
    },
  },
}
</script>

<style scoped>
.icon {
  color: rgba(0, 0, 0, 0.54);
}
</style>

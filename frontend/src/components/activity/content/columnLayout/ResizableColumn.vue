<template>
  <LayoutItem
    class="ec-resizable-col"
    :basis="basis"
    :grow="grow"
    :layout-mode="layoutMode"
    :class="{
      'ec-resizable-col--layout-mode': layoutMode,
      'ec-resizable-col--default': isDefaultVariant,
    }"
  >
    <template v-if="layoutMode && isDefaultVariant && showHeader">
      <resizable-column-header
        v-if="!last"
        class="ec-column-head"
        :width="width"
        :min-width="minWidth"
        :max-width="maxWidth"
        :column-height="columnHeight"
        v-on="$listeners"
      />
      <div v-else class="ec-column-head"></div>
    </template>

    <slot />
    <mobile-column-width-indicator
      v-if="layoutMode && numColumns > 1 && showHeader"
      :num-columns="numColumns"
      :width="width"
      :width-left="widthLeft"
      :width-right="widthRight"
      :slot-name="slotName"
      :show-progress="!isDefaultVariant"
    />
  </LayoutItem>
</template>

<script>
import ResizableColumnHeader from '@/components/activity/content/columnLayout/ResizableColumnHeader.vue'
import MobileColumnWidthIndicator from '@/components/activity/content/columnLayout/MobileColumnWidthIndicator.vue'
import LayoutItem from '@/components/activity/content/layout/LayoutItem.vue'

export default {
  name: 'ResizableColumn',
  components: {
    LayoutItem,
    MobileColumnWidthIndicator,
    ResizableColumnHeader,
  },
  props: {
    parentContentNode: { type: Object, required: true },
    layoutMode: { type: Boolean, required: true },
    widthLeft: { type: Number, required: true },
    width: { type: Number, required: true }, // the column width, in 1/12th units of the full width
    widthRight: { type: Number, required: true },
    numColumns: { type: Number, default: 1 },
    last: { type: Boolean, default: false }, // whether this is the last column
    minWidth: { type: Number, default: 3 }, // minimum allowed width of this column
    maxWidth: { type: Number, default: 12 }, // maximum allowed width of this column
    color: { type: String, required: true },
    showHeader: { type: Boolean, default: true },
    isDefaultVariant: { type: Boolean, default: true },
    slotName: { type: [String, Number], required: true },
  },
  data() {
    return {
      columnHeight: 100,
    }
  },
  computed: {
    grow() {
      return this.width
    },
    basis() {
      return !this.isDefaultVariant ? '100%' : '0'
    },
  },
  updated() {
    this.columnHeight = this.$el.clientHeight
  },
}
</script>

<style scoped lang="scss">
.ec-resizable-col {
  width: 0;
  flex-direction: column-reverse;

  &.ec-resizable-col--layout-mode {
    padding-top: 4px;
  }

  &:not(.ec-resizable-col--layout-mode) {
    & + .ec-resizable-col--default:not(.ec-resizable-col--layout-mode) {
      border-left: 1px solid #ccc;
    }
    &
      + .ec-resizable-col:not(.ec-resizable-col--default):not(
        .ec-resizable-col--layout-mode
      ) {
      border-top: 1px solid #ccc;
    }
  }
}

.ec-column-head {
  position: relative;
}
</style>

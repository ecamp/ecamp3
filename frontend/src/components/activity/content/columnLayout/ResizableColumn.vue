<template>
  <v-col
    class="resizable-col"
    :class="{ [widthClass]: true, 'layout-mode': layoutMode, 'top-border': showHeader }">
    <resizable-column-header
      v-if="layoutMode && $vuetify.breakpoint.mdAndUp && showHeader"
      :width="width"
      :min-width="minWidth"
      :max-width="maxWidth"
      :column-height="columnHeight"
      v-on="$listeners">
      <menu-cardless-content-node v-if="last" :content-node="parentContentNode">
        <slot name="menu" />
      </menu-cardless-content-node>
    </resizable-column-header>

    <mobile-column-width-indicator
      v-if="layoutMode && $vuetify.breakpoint.smAndDown && numColumns > 1 && showHeader"
      :num-columns="numColumns"
      :width="width"
      :width-left="widthLeft"
      :width-right="widthRight"
      :color="color" />

    <slot />
  </v-col>
</template>

<script>
import ResizableColumnHeader from '@/components/activity/content/columnLayout/ResizableColumnHeader.vue'
import MobileColumnWidthIndicator from '@/components/activity/content/columnLayout/MobileColumnWidthIndicator.vue'
import MenuCardlessContentNode from '@/components/activity/MenuCardlessContentNode.vue'

export default {
  name: 'ResizableColumn',
  components: {
    MenuCardlessContentNode,
    MobileColumnWidthIndicator,
    ResizableColumnHeader
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
    showHeader: { type: Boolean, default: true }
  },
  data () {
    return {
      columnHeight: 100
    }
  },
  computed: {
    widthClass () {
      if (this.$vuetify.breakpoint.smAndDown) return 'col-12'
      return 'col-md-' + this.width
    }
  },
  updated () {
    this.columnHeight = this.$el.clientHeight
  }
}
</script>

<style scoped lang="scss">
.resizable-col {
  @media #{map-get($display-breakpoints, 'sm-and-down')} {
    &.top-border {
      border-top: 1px solid rgba(0, 0, 0, 0.32);
    }
  }

  &:not(.layout-mode) {
    @media #{map-get($display-breakpoints, 'md-and-up')} {
      & + .resizable-col:not(.layout-mode) {
        border-left: 1px solid rgba(0, 0, 0, 0.12);
      }
    }
  }
}
</style>

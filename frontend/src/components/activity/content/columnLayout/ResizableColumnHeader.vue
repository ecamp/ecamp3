<template>
  <div>
    <slot>
      <v-btn
        icon
        text
        color="primary"
        class="resize-btn"
        :class="{ dragging }"
        :style="cssVariables"
        @mousedown.stop.prevent="mouseDown"
        @touchstart.stop.prevent="mouseDown"
      >
        <v-icon> mdi-arrow-split-vertical </v-icon>
      </v-btn>
    </slot>
  </div>
</template>
<script>
export default {
  name: 'ResizableColumnHeader',
  props: {
    width: { type: Number, required: true }, // the column width, in 1/12th units of the full width
    columnHeight: { type: Number, default: 200 },
    minWidth: { type: Number, default: 3 },
    maxWidth: { type: Number, default: 12 },
  },
  data() {
    return {
      dragging: false,
      startWidth: 0,
      startValue: this.width,
    }
  },
  computed: {
    cssVariables() {
      return '--column-height: ' + this.columnHeight + 'px'
    },
  },
  watch: {
    dragging() {
      if (this.dragging) document.body.classList.add('dragging')
      else document.body.classList.remove('dragging')
    },
  },
  mounted() {
    document.documentElement.addEventListener('mousemove', this.mouseMove)
    document.documentElement.addEventListener('mouseup', this.mouseUp)
    document.documentElement.addEventListener('mouseleave', this.mouseUp)
    document.documentElement.addEventListener('touchmove', this.mouseMove, true)
    document.documentElement.addEventListener('touchend', this.mouseUp, true)
    document.documentElement.addEventListener('touchcancel', this.mouseUp, true)
    document.documentElement.addEventListener('touchstart', this.mouseUp, true)
  },
  beforeDestroy: function () {
    document.documentElement.removeEventListener('mousemove', this.mouseMove)
    document.documentElement.removeEventListener('mouseup', this.mouseUp)
    document.documentElement.removeEventListener('mouseleave', this.mouseUp)
    document.documentElement.removeEventListener('touchmove', this.mouseMove, true)
    document.documentElement.removeEventListener('touchend', this.mouseUp, true)
    document.documentElement.removeEventListener('touchcancel', this.mouseUp, true)
    document.documentElement.removeEventListener('touchstart', this.mouseUp, true)
  },
  methods: {
    mouseDown: function (ev) {
      ev.stopPropagation()

      this.dragging = true
      this.startWidth = this.$el.clientWidth
      this.startValue = this.width
      this.$emit('resize-start')
    },
    mouseMove(ev) {
      if (!this.dragging) return
      ev.stopPropagation()

      this.resizing(ev)
    },
    mouseUp(ev) {
      if (!this.dragging) return
      ev.stopPropagation()

      this.resizing(ev)

      this.dragging = false
      this.startWidth = 0
      this.startValue = this.width
      this.$emit('resize-stop')
    },
    resizing(ev) {
      const pageX = typeof ev.pageX !== 'undefined' ? ev.pageX : ev.touches[0].pageX
      const newWidthPx = pageX - this.$el.getBoundingClientRect().left
      const newWidth = this.limit(
        this.snapToGrid((newWidthPx / this.startWidth) * this.startValue)
      )
      if (newWidth !== this.width) this.$emit('resizing', newWidth)
    },
    snapToGrid(width) {
      return Math.round(width)
    },
    limit(width) {
      return Math.min(Math.max(width, this.minWidth), this.maxWidth)
    },
  },
}
</script>
<style scoped lang="scss">
.resize-btn {
  position: absolute;
  right: -35px;
  top: -48px;
  z-index: 2;
  cursor: pointer;
  border: 1px solid transparent !important;
  transition: color 0.2s ease;
  .v-icon {
    transition: color 0.2s ease;
  }
  &:active,
  &:hover {
    color: map-get($blue, 'darken-4') !important;
    cursor: col-resize;
  }
  &::after {
    position: absolute;
    top: calc(-1 * var(--column-height) + 48px);
    left: calc(50% - 1px - 12px);
    border-left: 12px solid transparent;
    border-right: 12px solid transparent;
    background-clip: content-box;
    box-sizing: content-box;
    display: block;
    height: calc(var(--column-height) - 48px);
    width: 2px;
    background-image: linear-gradient(
      to bottom,
      rgb(21, 101, 192, 0.1),
      rgb(21, 101, 192, 0.1) 40%,
      map-get($blue, 'darken-3') 40%,
      map-get($blue, 'darken-3') 100%
    );
    border-image: linear-gradient(
        to right,
        transparent 0%,
        map-get($blue, 'lighten-2') 46%,
        map-get($blue, 'lighten-2') 53%,
        transparent 100%
      )
      1;
    background-size: 100% 16px;
    background-repeat: repeat, repeat;
    content: '';
    filter: saturate(0.2) brightness(2.2);
    transition: filter 0.2s ease;
  }
  &.dragging {
    cursor: move;
  }
  &:hover::after,
  &:active::after,
  &:focus::after,
  &.dragging::after {
    filter: saturate(1) brightness(1);
  }
}
</style>

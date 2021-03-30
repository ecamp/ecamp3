<template>
  <v-col class="resizable-col" :class="{ [widthClass]: true, 'layout-mode': layoutMode }">
    <div v-if="layoutMode" class="resize-spacer">
      <v-btn v-if="!last && $vuetify.breakpoint.mdAndUp"
             icon
             outlined
             color="primary"
             class="resize-btn"
             :class="{ dragging }"
             @mousedown.stop.prevent="mouseDown"
             @touchstart.stop.prevent="mouseDown">
        <v-icon>
          mdi-arrow-split-vertical
        </v-icon>
      </v-btn>
    </div>
    <slot />
  </v-col>
</template>

<script>
export default {
  name: 'ResizableColumn',
  props: {
    layoutMode: { type: Boolean, required: true },
    width: { type: Number, required: true }, // the column width, in 1/12th units of the full width
    last: { type: Boolean, default: false }, // whether this is the last column
    minWidth: { type: Number, default: 3 }, // minimum allowed width of this column
    maxWidth: { type: Number, default: 12 } // maximum allowed width of this column
  },
  data () {
    return {
      dragging: false,
      startWidth: 0,
      startValue: this.width
    }
  },
  computed: {
    widthClass () {
      if (this.$vuetify.breakpoint.smAndDown) return 'col-12'
      return 'col-md-' + this.width
    }
  },
  watch: {
    dragging () {
      if (this.dragging) document.body.classList.add('dragging')
      else document.body.classList.remove('dragging')
    }
  },
  mounted () {
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
      if (!this.layoutMode) return
      ev.stopPropagation()

      this.dragging = true
      this.startWidth = this.$el.clientWidth
      this.startValue = this.width
      this.$emit('resize-start')
    },
    mouseMove (ev) {
      if (!this.dragging) return
      ev.stopPropagation()

      this.resizing(ev)
    },
    mouseUp (ev) {
      if (!this.dragging) return
      ev.stopPropagation()

      this.resizing(ev)

      this.dragging = false
      this.startWidth = 0
      this.startValue = this.width
      this.$emit('resize-stop')
    },
    resizing (ev) {
      const pageX = typeof ev.pageX !== 'undefined' ? ev.pageX : ev.touches[0].pageX
      const newWidthPx = pageX - this.$el.getBoundingClientRect().left
      const newWidth = this.limit(this.snapToGrid(newWidthPx / this.startWidth * this.startValue))
      if (newWidth !== this.width) this.$emit('resizing', newWidth)
    },
    snapToGrid (width) {
      return Math.round(width)
    },
    limit (width) {
      return Math.min(Math.max(width, this.minWidth), this.maxWidth)
    }
  }
}
</script>

<style scoped lang="scss">
.resize-spacer {
  height: 60px;
  position: relative;

  @media #{map-get($display-breakpoints, 'sm-and-down')} {
    display: none;
  }
}
.resize-btn {
  position: absolute;
  right: -16px;
  top: 13px;
  z-index: 100;
  cursor: pointer;

  &::after {
    opacity: 0;
    position: absolute;
    top: 115%;
    left: 50%;
    display: block;
    height: 10rem;
    width: 2px;
    background-image: linear-gradient(to bottom, transparent, transparent 60%, #fff 60%, #fff 100%), linear-gradient(to bottom, map-get($blue, 'lighten-2'), map-get($blue, 'lighten-2'), transparent);
    background-size: 100% 15px, 100% 100%;
    background-repeat: repeat, repeat;
    content: '';
    transition: opacity 0.2s ease;
  }

  &.dragging {
    cursor: move;
  }

  &:hover::after, &:active::after, &:focus::after, &.dragging::after {
    opacity: 100%;
  }
}
.fullwidth {
  width: 100%;
}
</style>

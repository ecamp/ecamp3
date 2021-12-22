<template>
  <content-node
    :content-node="contentNode"
    :layout-mode="layoutMode"
    :disable="disabled" />
</template>

<script>
import ContentNode from '@/components/activity/ContentNode.vue'

export default {
  name: 'RootNode',
  components: {
    ContentNode
  },
  provide () {
    return {
      draggableDirty: {
        isDirty: this.isDirty,
        setDirty: this.setDirty,
        clearDirty: this.clearDirty
      }
    }
  },
  props: {
    contentNode: { type: Object, required: true },
    layoutMode: { type: Boolean, required: true },
    disabled: { type: Boolean, default: false }
  },
  data () {
    return {
      dirtyTimestamp: null // if not null, all DraggableContentNodes behave as if dirty
    }
  },
  methods: {
    setDirty (timestamp) {
      this.dirtyTimestamp = timestamp
    },
    clearDirty (timestamp) {
      // only clear dirty flag if it was set by the same timestamp (or override if timestamp parameter is null)
      if (this.dirtyTimestamp === timestamp || timestamp === null) {
        this.dirtyTimestamp = null
      }
    },
    isDirty () {
      return this.dirtyTimestamp !== null
    }
  }
}
</script>

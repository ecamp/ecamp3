<template>
  <content-node
    :content-node="contentNode"
    :layout-mode="layoutMode"
    :disable="disabled"
    @setDirty="setDirty"
    @clearDirty="clearDirty" />
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
      draggableDirty: this.draggableDirty
    }
  },
  props: {
    contentNode: { type: Object, required: true },
    layoutMode: { type: Boolean, required: true },
    disabled: { type: Boolean, default: false }
  },
  data () {
    return {
      draggableDirty: {
        flag: false, // if true, all DraggableContentNodes behave as if dirty
        timestamp: null
      }
    }
  },
  methods: {
    setDirty (timestamp) {
      this.draggableDirty.flag = true
      this.draggableDirty.timestamp = timestamp
    },
    clearDirty (timestamp) {
      // only clear dirty flag if it was set by the same timestamp (or override if timestamp is null)
      if (this.draggableDirty.timestamp === timestamp || timestamp === null) {
        this.draggableDirty.flag = false
      }
    }
  }
}
</script>

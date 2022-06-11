<template>
  <div>
    <draggable v-if="contentNodeIds"
               v-model="localContentNodeIds"
               :disabled="!draggingEnabled"
               group="contentNodes"
               class="draggable-area d-flex flex-column pb-10"
               :class="{ 'min-height': layoutMode }"
               :invert-swap="true"
               @start="startDrag"
               @add="finishDrag"
               @update="finishDrag"
               @end="cleanupDrag">
      <content-node v-for="id in draggableContentNodeIds"
                    :key="id"
                    :data-href="allContentNodesById[id]._meta.self"
                    class="content-node"
                    :content-node="allContentNodesById[id]"
                    :layout-mode="layoutMode"
                    :draggable="draggingEnabled"
                    :disabled="disabled" />
    </draggable>

    <button-nested-content-node-add v-if="layoutMode"
                                    :layout-mode="layoutMode"
                                    :parent-content-node="parentContentNode"
                                    :slot-name="slotName" />
  </div>
</template>
<script>
import { keyBy, sortBy } from 'lodash'
import Draggable from 'vuedraggable'
import ButtonNestedContentNodeAdd from '@/components/activity/ButtonNestedContentNodeAdd.vue'

export default {
  name: 'DraggableContentNodes',
  components: {
    Draggable,
    ButtonNestedContentNodeAdd,
    // Lazy import necessary due to recursive component structure
    ContentNode: () => import('@/components/activity/ContentNode.vue')
  },
  inject: ['draggableDirty', 'allContentNodes'],
  props: {
    layoutMode: { type: Boolean, default: false },
    slotName: { type: String, required: true },
    parentContentNode: { type: Object, required: true },
    disabled: { type: Boolean, default: false }
  },
  data () {
    return {
      localContentNodeIds: []
    }
  },
  computed: {
    allContentNodesById () {
      return keyBy(this.allContentNodes().items, 'id')
    },
    draggingEnabled () {
      return this.layoutMode && this.$vuetify.breakpoint.mdAndUp && !this.disabled
    },
    contentNodeIds () {
      return sortBy(
        // We have to work with the complete list of contentNodes instead of parentContentNode.children()
        // in order to allow dragging a node to a new parent
        this.allContentNodes().items
          .filter(child => child.slot === this.slotName && child.parent !== null && child.parent()._meta.self === this.parentContentNode._meta.self),
        'position'
      ).map(child => child.id)
    },
    draggableContentNodeIds () {
      return this.localContentNodeIds.filter(id => id in this.allContentNodesById)
    }
  },
  watch: {
    contentNodeIds: {
      immediate: true,
      handler () {
        // update local sorting with external sorting if not dirty
        if (!this.draggableDirty.isDirty()) {
          this.localContentNodeIds = this.contentNodeIds
        }
      }
    }
  },
  beforeDestroy () {
    this.cleanupDrag()
  },
  methods: {
    startDrag () {
      document.body.classList.add('dragging', 'dragging-content-node')
      document.documentElement.addEventListener('mouseup', this.cleanupDrag)
    },
    async finishDrag (event) {
      this.cleanupDrag()

      // set dirty flag
      const timestamp = Date.now()
      this.draggableDirty.setDirty(timestamp)

      // patch content node location
      await this.api.patch(event.item.dataset.href, {
        slot: this.slotName,
        parent: this.parentContentNode._meta.self,
        position: event.newDraggableIndex
      })

      // reload all contentNodes to update position properties
      await this.allContentNodes().$reload()

      // clear dirty flag (unless a new change happened in the meantime)
      this.draggableDirty.clearDirty(timestamp)
    },
    cleanupDrag () {
      document.body.classList.remove('dragging', 'dragging-content-node')
      document.documentElement.removeEventListener('mouseup', this.cleanupDrag)
    }
  }
}
</script>
<style scoped lang="scss">
.min-height {
  min-height: 10rem;
}

.dragging-content-node .draggable-area {
  position: relative;
  z-index: 100;

  &::after {
    pointer-events: none;
    display: block;
    position: absolute;
    top: 4px;
    bottom: 4px;
    left: 4px;
    right: 4px;
    border-radius: 5px;
    border: 2px dashed map-get($blue-grey, 'base');
    background: map-get($blue-grey, 'lighten-4');
    opacity: 40%;
    content: '';
  }
}
</style>

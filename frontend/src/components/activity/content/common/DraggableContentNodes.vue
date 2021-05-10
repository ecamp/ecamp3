<template>
  <div>
    <draggable v-if="contentNodeIds"
               v-model="localContentNodeIds"
               :disabled="!draggingEnabled"
               group="contentNodes"
               class="d-flex flex-column"
               :class="{ 'column-min-height': layoutMode }"
               @start="startDrag"
               @add="finishDrag"
               @update="finishDrag"
               @remove="finishDrag(false)">
      <content-node v-for="id in draggableContentNodeIds"
                    :key="id"
                    class="content-node"
                    :content-node="allContentNodesById[id]"
                    :layout-mode="layoutMode"
                    :draggable="draggingEnabled" />
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
import ButtonNestedContentNodeAdd from '@/components/activity/content/common/ButtonNestedContentNodeAdd.vue'

export default {
  name: 'DraggableContentNodes',
  components: {
    Draggable,
    ButtonNestedContentNodeAdd,
    // Lazy import necessary due to recursive component structure
    ContentNode: () => import('@/components/activity/ContentNode.vue')
  },
  props: {
    layoutMode: { type: Boolean, default: false },
    slotName: { type: String, required: true },
    parentContentNode: { type: Object, required: true }
  },
  data () {
    return {
      localContentNodeIds: []
    }
  },
  computed: {
    allContentNodesById () {
      return keyBy(this.parentContentNode.owner().contentNodes().items, 'id')
    },
    draggingEnabled () {
      return this.layoutMode && this.$vuetify.breakpoint.mdAndUp
    },
    contentNodeIds () {
      return sortBy(
        // We have to work with the complete list of contentNodes instead of parentContentNode.children()
        // in order to allow dragging a node to a new parent
        this.parentContentNode.owner().contentNodes().items
          .filter(child => child.slot === this.slotName && child.parent().id === this.parentContentNode.id),
        'position'
      ).map(child => child.id)
    },
    draggableContentNodeIds () {
      return this.localContentNodeIds.filter(id => id in this.allContentNodesById)
    }
  },
  watch: {
    contentNodeIds () {
      this.localContentNodeIds = this.contentNodeIds
    }
  },
  methods: {
    startDrag () {
      document.body.classList.add('dragging')
    },
    finishDrag (save = true) {
      document.body.classList.remove('dragging')
      if (save) this.saveReorderedChildren()
    },
    async saveReorderedChildren () {
      let position = 0
      const payload = Object.fromEntries(this.draggableContentNodeIds.map(id => [id, {
        slot: this.slotName,
        position: position++,
        parentId: this.parentContentNode.id
      }]))
      this.api.patch(await this.api.href(this.parentContentNode.owner(), 'contentNodes'), payload)
    }
  }
}
</script>
<style scoped>
.column-min-height {
  min-height: 10rem;
}
</style>

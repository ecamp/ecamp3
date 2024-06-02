<template>
  <div class="ec-draggable-content-nodes d-flex flex-column flex-grow-1 relative">
    <draggable
      v-if="contentNodeIds"
      v-model="localContentNodeIds"
      :disabled="!draggingEnabled"
      :group="{ name: 'contentNodes', put: responsiveLayoutsOnlyInRoot }"
      class="draggable-area flex-grow-1"
      :class="{
        'min-height draggable-area--layout-mode': layoutMode,
        'draggable-area--read-mode': !layoutMode,
        'draggable-area--root': isRoot,
        'draggable-area--column d-flex flex-column': direction === 'column',
        'draggable-area--row d-flex flex-row flex-wrap': direction === 'row',
        'absolute inset-0': layoutMode && draggableContentNodeIds.length === 0,
      }"
      :swap-threshold="0.65"
      :inverted-swap-threshold="0.65"
      @start="startDrag"
      @add="finishDrag"
      @update="finishDrag"
      @end="cleanupDrag"
    >
      <content-node
        v-for="id in draggableContentNodeIds"
        :key="id"
        :data-href="allContentNodesById[id]._meta.self"
        :data-type="allContentNodesById[id].contentTypeName"
        class="content-node"
        :content-node="allContentNodesById[id]"
        :layout-mode="layoutMode"
        :draggable="draggingEnabled"
        :disabled="disabled"
      />
      <v-sheet
        v-if="!layoutMode && draggableContentNodeIds.length === 0"
        elevation="0"
        class="content-node placeholder-node"
      ></v-sheet>
    </draggable>

    <ButtonNestedContentNodeAdd
      v-if="layoutMode && !disabled"
      :class="{ 'flex-grow-0': draggableContentNodeIds.length !== 0 }"
      :layout-mode="layoutMode"
      :parent-content-node="parentContentNode"
      :slot-name="slotName"
      :root="isRoot"
      :single="!compact && draggableContentNodeIds.length === 0"
    />
  </div>
</template>
<script>
import { keyBy, sortBy } from 'lodash'
import Draggable from 'vuedraggable'
import ButtonNestedContentNodeAdd from '@/components/activity/ButtonNestedContentNodeAdd.vue'
import { errorToMultiLineToast } from '@/components/toast/toasts'

export default {
  name: 'DraggableContentNodes',
  components: {
    Draggable,
    ButtonNestedContentNodeAdd,
    // Lazy import necessary due to recursive component structure
    ContentNode: () => import('@/components/activity/ContentNode.vue'),
  },
  inject: ['draggableDirty', 'allContentNodes'],
  props: {
    layoutMode: { type: Boolean, default: false },
    slotName: { type: String, required: true },
    parentContentNode: { type: Object, required: true },
    disabled: { type: Boolean, default: false },
    direction: { type: String, default: 'column' },
    isRoot: { type: Boolean, default: false },
    compact: { type: Boolean, default: false },
  },
  data() {
    return {
      localContentNodeIds: [],
    }
  },
  computed: {
    allContentNodesById() {
      return keyBy(this.allContentNodes().items, 'id')
    },
    draggingEnabled() {
      return this.layoutMode && this.$vuetify.breakpoint.mdAndUp && !this.disabled
    },
    contentNodeIds() {
      return sortBy(
        // We have to work with the complete list of contentNodes instead of parentContentNode.children()
        // in order to allow dragging a node to a new parent
        this.allContentNodes().items.filter(
          (child) =>
            child.slot === this.slotName &&
            child.parent !== null &&
            child.parent()._meta.self === this.parentContentNode._meta.self
        ),
        'position'
      ).map((child) => child.id)
    },
    draggableContentNodeIds() {
      return this.localContentNodeIds.filter((id) => id in this.allContentNodesById)
    },
  },
  watch: {
    contentNodeIds: {
      immediate: true,
      handler() {
        // update local sorting with external sorting if not dirty
        if (!this.draggableDirty.isDirty()) {
          this.localContentNodeIds = this.contentNodeIds
        }
      },
    },
  },
  beforeDestroy() {
    this.cleanupDrag()
  },
  methods: {
    responsiveLayoutsOnlyInRoot(to, from, item) {
      return (
        item.dataset.type !== 'ResponsiveLayout' ||
        to.el.classList.contains('draggable-area--root')
      )
    },
    startDrag(event) {
      document.body.classList.add('dragging', 'dragging-content-node')
      if (event.item.dataset.type === 'ResponsiveLayout') {
        document.body.classList.add('dragging-responsive-layout')
      }
      document.documentElement.addEventListener('mouseup', this.cleanupDrag)
    },
    async finishDrag(event) {
      this.cleanupDrag()

      // set dirty flag
      const timestamp = Date.now()
      this.draggableDirty.setDirty(timestamp)

      // patch content node location
      try {
        await this.api.patch(event.item.dataset.href, {
          slot: this.slotName,
          parent: this.parentContentNode._meta.self,
          position: event.newDraggableIndex,
        })
      } catch (e) {
        this.$toast.error(errorToMultiLineToast(e))
      }

      // reload all contentNodes to update position properties
      await this.allContentNodes().$reload()

      // clear dirty flag (unless a new change happened in the meantime)
      this.draggableDirty.clearDirty(timestamp)
    },
    cleanupDrag() {
      document.body.classList.remove(
        'dragging',
        'dragging-content-node',
        'dragging-responsive-layout'
      )
      document.documentElement.removeEventListener('mouseup', this.cleanupDrag)
    },
  },
}
</script>
<style scoped lang="scss">
.min-height {
  min-height: 10rem;
}

.draggable-area ::v-deep(.content-node) {
  margin: 0 !important;
  flex-grow: 1;
  transition: all 0.4s 25ms ease;
  transition-property: background-color, border-color, box-shadow;
}

.draggable-area--layout-mode :deep(.content-node) {
  border: 1px solid rgb(0 0 0 / 28%);
}

.draggable-area--layout-mode :deep(.content-node.draggable.sortable-chosen) {
  background-color: white;
}

@supports selector(:has(+ *)) {
  .draggable-area--layout-mode :deep(.content-node:has(.content-node:hover)) {
    background: white;
  }
}

@supports selector(:has(+ *)) {
  .draggable-area--layout-mode
    ::v-deep(
      .content-node:hover:is(
          :not(:has(.content-node:hover)):not(:has(.ec-button-contentnode-add:hover)):not(
              :has(.resize-btn:hover)
            )
        )
    ) {
    box-shadow:
      0 4px 6px -3px rgba(0, 0, 0, 0.2),
      0 8px 14px 1px rgba(0, 0, 0, 0.14),
      0 2px 18px 3px rgba(0, 0, 0, 0.12) !important;
  }
}

.draggable-area--row ::v-deep(.content-node) {
  flex: 1 0 320px !important;
}

.draggable-area--layout-mode {
  display: flex !important;
  gap: 6px 4px;
  margin-bottom: 6px;
}

.draggable-area--read-mode {
  gap: 1px;
}

.dragging-content-node:not(.dragging-responsive-layout) {
  .draggable-area:not(.draggable-area--root) {
    position: relative;
    z-index: 100;

    &::after {
      pointer-events: none;
      display: block;
      position: absolute;
      top: -4px;
      bottom: -4px;
      left: -4px;
      right: -4px;
      opacity: 40%;
      border-radius: 8px;
      border: 2px dotted map-get($blue, 'darken-2');
      opacity: 40%;
      content: '';
    }
  }
}
</style>

<template>
  <div
    v-if="!disabled || sortedItems.length"
    class="e-checklist-dragarea"
    :class="{ dragging }"
  >
    <draggable
      class="e-checklist-dragarea--inner"
      :list="sortedItems"
      ghost-class="e-sortable-checklist-item--ghost"
      handle=".drag-and-drop-handle"
      filter=".add-item"
      :disabled="disabled"
      :data-parent="parentKey"
      group="checklist"
      :sort="true"
      :item-key="(element) => element"
      @start="dragStart"
      @end="dragStop"
    >
      <template #item="{ element, index }">
        <SortableChecklistItem
          :key="element._meta.self"
          :data-href="element._meta.self"
          :checklist="checklist"
          :item="element"
          :item-position="index"
          :disabled="disabled"
          @drag-start="dragStart"
          @drag-end="dragEnd"
        />
      </template>
    </draggable>
    <ChecklistItemCreate
      v-if="!disabled && !(parentDragging || dragging)"
      class="add-item"
      :checklist="checklist"
      :parent="parent?._meta.self"
    >
      <template #activator="{ props }">
        <v-list-item
          class="e-sortable-checklist-item__add ml-10 mr-2 my-n1 px-0 rounded-pill min-h-0"
          v-bind="props"
        >
          <template #prepend>
            <v-avatar class="mr-2" size="32">
              <v-icon color="currentColor">mdi-plus</v-icon>
            </v-avatar>
          </template>
          <v-list-item-title>{{
            $t('components.checklist.sortableChecklist.add', {
              parent: parent?.text ?? checklist.name,
            })
          }}</v-list-item-title>
        </v-list-item>
      </template>
    </ChecklistItemCreate>
  </div>
</template>

<script>
import { computed } from 'vue'
import draggable from 'vuedraggable'
import { sortBy, filter } from 'lodash-es'
import { errorToMultiLineToast } from '@/components/toast/toasts.js'
import SortableChecklistItem from '@/components/checklist/SortableChecklistItem.vue'
import ChecklistItemCreate from '@/components/checklist/ChecklistItemCreate.vue'
import { useToast } from '@/components/toast/useToast.js'

export default {
  name: 'SortableChecklist',
  components: {
    ChecklistItemCreate,
    SortableChecklistItem,
    draggable,
  },
  inject: {
    parentDragging: {
      from: 'parentDragging',
      default: false,
    },
  },
  provide() {
    return {
      parentDragging: computed(() => this.parentDragging || this.dragging),
    }
  },
  props: {
    checklist: { type: Object, required: true },
    parent: { type: Object, default: null },
    disabled: { type: Boolean, default: false },
  },
  emits: ['dragStart', 'dragEnd'],
  setup() {
    const toast = useToast()
    return { toast }
  },
  data() {
    return {
      dragging: false,
    }
  },
  computed: {
    // keys within items property, sorted by position (and key as fallback)
    sortedItems: {
      get() {
        return sortBy(
          filter(
            this.checklist.checklistItems().items,
            ({ parent }) => parent?.()._meta.self === this.parent?._meta.self
          ),
          'position'
        )
      },
    },
    parentKey() {
      return this.parent?._meta.self ? JSON.stringify(this.parent?._meta.self) : 'null'
    },
  },
  methods: {
    dragStart() {
      this.dragging = true
      this.$emit('dragStart')
    },
    dragEnd() {
      this.dragging = false
      this.$emit('dragEnd')
    },
    async dragStop(event) {
      this.dragging = false
      this.$emit('dragEnd')
      if (event.originalEvent.type === 'drop') {
        const parent = JSON.parse(event.to.dataset.parent)
        // patch content node location
        await this.api
          .patch(event.item.dataset.href, {
            position: event.newDraggableIndex,
            parent,
          })
          .catch((e) => {
            this.toast.error(errorToMultiLineToast(e))
          })
          .finally(async () => await this.checklist.checklistItems().$reload())
      } else {
        this.checklist.checklistItems().$reload()
      }
    },
  },
}
</script>

<style scoped>
.e-sortable-checklist-item--ghost {
  opacity: 0.5;
  background: rgb(196, 196, 196);
  filter: saturate(0);
}
:deep(.drag-and-drop-handle) {
  cursor: grab;
}
.e-checklist-dragarea {
  min-height: 20px;
  padding-bottom: 6px;
  display: grid;
}
.e-checklist-dragarea--inner {
  min-width: 0;
}
.e-checklist-dragarea.dragging:deep(.e-checklist-dragarea) {
  background: rgba(0, 130, 236, 0.15);
  padding-bottom: 0;
}
.e-sortable-checklist-item__add {
  min-width: 0;
  padding-top: 2px;
  padding-bottom: 2px;
}
.e-sortable-checklist-item__add:not(:hover):not(:focus-within) {
  opacity: 0.6;
}
.e-sortable-checklist-item__add:is(:hover, :focus-within) {
  color: #1976d2 !important;
}
.e-checklist-dragarea:not(:hover):not(:focus-within)
  :deep(.e-sortable-checklist-item__add) {
  display: none;
}
</style>

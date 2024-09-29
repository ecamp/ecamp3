<template>
  <div class="e-checklist-dragarea" :class="{ dragging }">
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
      @start="dragStart"
      @end="dragStop"
    >
      <SortableChecklistItem
        v-for="(item, i) in localSortedItems"
        :key="item._meta.self"
        :data-href="item._meta.self"
        :checklist="checklist"
        :item="item"
        :item-position="i"
        @drag-start="dragStart"
        @drag-end="dragEnd"
      />
    </draggable>
    <ChecklistItemCreate
      v-if="!(parentDragging || dragging)"
      class="add-item"
      :checklist="checklist"
      :parent="parent?._meta.self"
    >
      <template #activator="{ on }">
        <v-list-item
          class="e-sortable-checklist-item__add ml-10 mr-2 my-n1 px-0 rounded-pill min-h-0"
          v-on="on"
        >
          <v-avatar class="mr-2" size="32"
            ><v-icon color="currentColor">mdi-plus</v-icon></v-avatar
          >
          <v-list-item-content class="py-0">
            <v-list-item-title>{{
              $tc('components.checklist.sortableChecklist.add', null, {
                parent: parent?.text ?? checklist.name,
              })
            }}</v-list-item-title>
          </v-list-item-content>
        </v-list-item>
      </template>
    </ChecklistItemCreate>
  </div>
</template>

<script>
import { computed } from 'vue'
import draggable from 'vuedraggable'
import { every, sortBy, filter } from 'lodash'
import { errorToMultiLineToast } from '@/components/toast/toasts.js'
import SortableChecklistItem from '@/components/checklist/SortableChecklistItem.vue'
import ChecklistItemCreate from '@/components/checklist/ChecklistItemCreate.vue'

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
  data() {
    return {
      dragging: false,
      dirty: false,
      savingRequest: 0,
      localSortedItems: [],
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
  watch: {
    sortedItems: {
      handler: function (newItems) {
        // update local sorting with external sorting if not dirty
        // or if number of items don't match (new incoming items or deleted items)
        if (!this.dirty || newItems.length !== this.localSortedItems.length) {
          this.localSortedItems = newItems
          this.dirty = false
          // remove dirty flag if external sorting is equal to local sorting (e.g. saving to API was successful)
        } else if (
          every(
            this.localSortedItems,
            (item, key) => item._meta.self === newItems[key]._meta.self
          )
        ) {
          this.dirty = false
        }
      },
      immediate: true,
    },
  },
  methods: {
    dragStart() {
      this.dragging = true
      this.$emit('drag-start')
    },
    dragEnd() {
      this.dragging = false
      this.$emit('drag-end')
    },
    async dragStop(event) {
      this.dragging = false
      this.$emit('drag-end')
      if (event.originalEvent.type === 'drop') {
        this.dirty = true
        this.savingRequest++
        const parent = JSON.parse(event.to.dataset.parent)
        // patch content node location
        await this.api
          .patch(event.item.dataset.href, {
            position: event.newDraggableIndex,
            parent,
          })
          .catch((e) => {
            this.$toast.error(errorToMultiLineToast(e))
          })
          .finally(async () => await this.checklist.checklistItems().$reload())
        this.savingRequest--
        if (this.savingRequest === 0) {
          this.dirty = false
        }
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

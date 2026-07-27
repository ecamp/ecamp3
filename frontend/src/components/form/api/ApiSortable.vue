<template>
  <draggable
    :model-value="localSortedItems"
    ghost-class="ghost"
    handle=".drag-and-drop-handle"
    tag="transition-group"
    :component-data="{ name: !dragging ? 'flip-list' : null }"
    :animation="200"
    :force-fallback="true"
    :disabled="disabled"
    @start="dragging = true"
    @end="dragging = false"
    @update="finishDrag"
  >
    <template #item="{ element, index }">
      <div :key="element._meta.self" :data-href="element._meta.self">
        <slot :item-position="index" :item="element" />
      </div>
    </template>
  </draggable>
</template>

<script>
import draggable from 'vuedraggable'
import { every, sortBy } from 'lodash-es'
import { errorToMultiLineToast } from '@/components/toast/toasts.js'
import { useToast } from '@/components/toast/useToast.js'

export default {
  name: 'ApiSortable',
  components: {
    draggable,
  },
  props: {
    endpoint: { type: Object, required: true },
    disabled: { type: Boolean, default: false },
  },
  setup() {
    const toast = useToast()
    return { toast }
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
    sortedItems() {
      return sortBy(this.endpoint.items, 'position')
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
    async finishDrag(event) {
      this.dirty = true
      this.savingRequest++
      // patch content node location
      await this.api
        .patch(event.item.dataset.href, {
          position: event.newDraggableIndex + 1,
        })
        .catch((e) => {
          this.toast.error(errorToMultiLineToast(e))
        })
        .finally(async () => {
          await this.endpoint.$reload()
          this.localSortedItems = this.endpoint.items
        })
      this.savingRequest--
      if (this.savingRequest === 0) {
        this.dirty = false
      }
    },
  },
}
</script>

<style scoped>
/* eslint-disable-next-line vue-scoped-css/no-unused-selector */
.flip-list-move {
  transition: transform 0.5s;
  opacity: 0.5;
  background: #c8ebfb;
}

/* eslint-disable-next-line vue-scoped-css/no-unused-selector */
.ghost {
  opacity: 0.5;
  background: #c8ebfb;
}

:deep(.drag-and-drop-handle) {
  cursor: grab;
}
</style>

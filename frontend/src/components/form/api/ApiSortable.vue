<template>
  <draggable
    v-model="localSortedKeys"
    ghost-class="ghost"
    handle=".drag-and-drop-handle"
    :animation="200"
    :force-fallback="true"
    :disabled="disabled"
    @sort="onSort"
    @start="dragging = true"
    @end="dragging = false">
    <!-- disable transition for drag&drop as draggable already comes with its own anmations -->
    <transition-group :name="!dragging ? 'flip-list' : null" tag="div">
      <div v-for="key in localSortedKeys" :key="key">
        <slot
          :itemKey="key"
          :item="items[key]"
          :on="eventHandlers" />
      </div>
    </transition-group>
  </draggable>
</template>

<script>
import draggable from 'vuedraggable'
import { isEqual } from 'lodash'

export default {
  name: 'ApiSortable', // TODO: consider renaming the component, as the logic of this compoentn is now independent of the API itself
  components: {
    draggable
  },
  props: {
    items: { type: Object, required: true },
    disabled: { type: Boolean, default: false }
  },
  data () {
    return {
      dragging: false,
      dirty: false,
      localSortedKeys: [],
      eventHandlers: {
        moveUp: this.moveUp,
        moveDown: this.moveDown,
        delete: this.delete
      }
    }
  },
  computed: {
    // keys within items property, sorted by position (and key as fallback)
    sortedKeys () {
      return Object.keys(this.items).sort((keyA, keyB) => {
        const positionA = this.items[keyA].position
        const positionB = this.items[keyB].position

        if (positionA !== positionB) {
          // firstly: sort by position property
          return positionA - positionB
        } else {
          // secondly: sort by id (string compare)
          return keyA.localeCompare(keyB)
        }
      })
    }
  },
  watch: {
    sortedKeys: {
      handler: function (sortedKeys) {
        // update local sorting with external sorting if not dirty
        // or if number of items don't match (new incoming items or deleted items)
        if (!this.dirty || sortedKeys.length !== this.localSortedKeys.length) {
          this.localSortedKeys = sortedKeys
          this.dirty = false

        // remove dirty flag if external sorting is equal to local sorting (e.g. saving to API was successful)
        } else if (isEqual(this.localSortedKeys, sortedKeys)) {
          this.dirty = false
        }
      },
      immediate: true
    }

  },
  methods: {
    async moveUp (key) {
      this.swapPosition(key, -1)
    },
    async moveDown (key) {
      this.swapPosition(key, +1)
    },

    // swaps position of entity with the element which is deltaPosition down/ahead in the list
    async swapPosition (key, deltaPosition) {
      const list = this.localSortedKeys
      const oldIndex = list.indexOf(key)

      const newIndex = oldIndex + deltaPosition

      // newIndex has the be within the list (cannot move first element up or last element down)
      if (newIndex >= 0 && newIndex < list.length) {
        // swap spaces in sortedKeys
        const movingListItem = list[oldIndex]
        this.$set(list, oldIndex, list[newIndex])
        this.$set(list, newIndex, movingListItem)

        this.onSort()
      }
    },

    async delete (key) {
      this.dirty = true

      // remove item from array of sorted keys
      const indexToDelete = this.localSortedKeys.indexOf(key)
      this.localSortedKeys.splice(indexToDelete, 1)

      // generate positions
      const payload = this.recalculatePositions()

      // replace deleted item with null value
      payload[key] = null

      this.$emit('sort', payload)
    },

    /**
     * Triggers on every sorting change
     */
    onSort () {
      this.dirty = true

      this.$emit('sort', this.recalculatePositions())
    },

    /**
     * Recalculates position properties based on current sorting and prepares patch payload
     */
    recalculatePositions () {
      const payload = {}
      let position = 1
      this.localSortedKeys.forEach(key => {
        payload[key] = { position }
        position++
      })

      return payload
    }

  }
}
</script>

<style scoped>

.flip-list-move {
  transition: transform 0.5s;
  opacity: 0.5;
  background: #c8ebfb;
}

.ghost {
  opacity: 0.5;
  background: #c8ebfb;
}

>>> .drag-and-drop-handle {
  cursor: grab;
}

</style>

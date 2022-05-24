<template>
  <draggable
    v-model="sorting.hrefList"
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
      <div v-for="entity in locallySortedEntities" :key="entity._meta.self">
        <slot :entity="entity" :on="eventHandlers" />
      </div>
    </transition-group>
  </draggable>
</template>

<script>
import draggable from 'vuedraggable'
import { isEqual } from 'lodash'

export default {
  name: 'ApiSortable',
  components: {
    draggable
  },
  props: {
    /* reference to sortable API collection */
    collection: { type: Function, required: true },
    disabled: { type: Boolean, default: false }
  },
  data () {
    return {
      dragging: false,
      sorting: {
        dirty: false,
        hrefList: []
      },
      eventHandlers: {
        moveUp: this.moveUp,
        moveDown: this.moveDown
      }
    }
  },
  computed: {
    // retrieve all relevant entities from external (incl. filtering and sorting)
    entities () {
      return this.collection().items.sort((a, b) => {
        if (a.position !== b.position) {
          // firstly: sort by position property
          return a.position - b.position
        } else {
          // secondly: sort by id (string compare)
          return a.id.localeCompare(b.id)
        }
      })
    },

    // locally sorted entities (sorted as per local hrefList)
    locallySortedEntities () {
      return this.sorting.hrefList.map((href) => this.api.get(href))
    }
  },
  watch: {
    entities: {
      handler: function (entities) {
        const hrefList = entities.map((entry) => entry._meta.self)

        // update local sorting with external sorting if not dirty
        // or if number of items don't match (new incoming items or deleted items)
        if (!this.sorting.dirty || hrefList.length !== this.sorting.hrefList.length) {
          this.sorting.hrefList = hrefList
          this.sorting.dirty = false

          // remove dirty flag if external sorting is equal to local sorting (e.g. saving to API was successful)
        } else if (isEqual(this.sorting.hrefList, hrefList)) {
          this.sorting.dirty = false
        }
      },
      immediate: true
    }
  },
  methods: {
    async moveUp (entity) {
      this.swapPosition(entity, -1)
    },
    async moveDown (entity) {
      this.swapPosition(entity, +1)
    },

    // swaps position of entity with the element which is deltaPosition down/ahead in the list
    async swapPosition (entity, deltaPosition) {
      const list = this.sorting.hrefList
      const oldIndex = list.indexOf(entity._meta.self)

      const newIndex = oldIndex + deltaPosition

      // newIndex has the be within the list (cannot move first element up or last element down)
      if (newIndex >= 0 && newIndex < list.length) {
        // swap spaces in hrefList
        const movingListItem = list[oldIndex]
        this.$set(list, oldIndex, list[newIndex])
        this.$set(list, newIndex, movingListItem)

        this.savePosition(entity, newIndex)
      }
    },

    /**
     * Triggers on every sorting change
     */
    async onSort (event) {
      const newIndex = event.newDraggableIndex
      const entity = this.api.get(this.sorting.hrefList[newIndex])

      this.savePosition(entity, newIndex)
    },

    /**
     * Saves new position to API and reloads complete list
     */
    async savePosition (entity, newPosition) {
      this.sorting.dirty = true

      await entity.$patch({
        position: newPosition
      })
      this.collection().$reload() // TODO: should $reload kill the last load and issue a new reload?
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

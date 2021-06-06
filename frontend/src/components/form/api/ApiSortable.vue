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
        <slot
          :entity="entity"
          :on="eventHandlers" />
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
        if (a.pos !== b.pos) {
          // firstly: sort by pos property
          return a.pos - b.pos
        } else {
          // secondly: sort by id (string compare)
          return a.id.localeCompare(b.id)
        }
      })
    },

    // locally sorted entities (sorted as per loal hrefList)
    locallySortedEntities () {
      return this.sorting.hrefList.map(href => this.api.get(href))
    }
  },
  watch: {
    entities: {
      handler: function (entities) {
        const hrefList = entities.map(entry => entry._meta.self)

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
      const list = this.sorting.hrefList
      const index = list.indexOf(entity._meta.self)

      // cannot move first entry up
      if (index > 0) {
        const previousItem = list[index - 1]
        this.$set(list, index - 1, list[index])
        this.$set(list, index, previousItem)
      }

      this.saveLocalSorting()
    },
    async moveDown (entity) {
      const list = this.sorting.hrefList
      const index = list.indexOf(entity._meta.self)

      // cannot move last entry down
      if (index >= 0 && index < (list.length - 1)) {
        const nextItem = list[index + 1]
        this.$set(list, index + 1, list[index])
        this.$set(list, index, nextItem)
      }

      this.saveLocalSorting()
    },

    /**
     * Triggeres on every sorting change
     */
    onSort (event) {
      this.saveLocalSorting()
    },

    /**
     * Saves local list sorting to API
     */
    saveLocalSorting () {
      this.sorting.dirty = true

      /**
      Compiles proper patchList object in the form of
      {
        'id1': { pos: 0 },
        'id2': { pos: 1 },
        ...
      }
      */
      const patchData = this.locallySortedEntities.map((entity, index) => [entity.id, { pos: index }])
      const patchDataObj = Object.fromEntries(patchData)

      this.collection().$patch(patchDataObj)
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

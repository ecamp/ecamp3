<template>
  <v-row v-if="!contentNode.loading" no-gutters>
    <resizable-column v-for="(_, slot) in columns"
                      :key="slot"
                      :parent-content-node="contentNode"
                      :layout-mode="layoutMode"
                      :width-left="relativeColumnWidths[slot][0]"
                      :width="relativeColumnWidths[slot][1]"
                      :width-right="relativeColumnWidths[slot][2]"
                      :num-columns="numColumns"
                      :last="slot === lastColumn"
                      :min-width="minWidth(slot)"
                      :max-width="maxWidth(slot)"
                      :color="color"
                      :show-header="!isRoot"
                      @resizing="newWidth => resizeColumn(slot, newWidth)"
                      @resize-stop="saveColumnWidths">
      <draggable-content-nodes :slot-name="slot"
                               :layout-mode="layoutMode"
                               :parent-content-node="contentNode"
                               :disabled="disabled" />

      <template #menu>
        <column-operations :content-node="contentNode" :min-column-width="minWidth(slot)" :total-width="12" />
      </template>
    </resizable-column>
  </v-row>
</template>

<script>

import { keyBy, mapValues } from 'lodash'
import { contentNodeMixin } from '@/mixins/contentNodeMixin.js'
import ResizableColumn from '@/components/activity/content/columnLayout/ResizableColumn.vue'
import DraggableContentNodes from '@/components/activity/DraggableContentNodes.vue'
import ColumnOperations from '@/components/activity/content/columnLayout/ColumnOperations.vue'

function cumulativeSumReducer (cumSum, nextElement) {
  cumSum.push((cumSum[cumSum.length - 1] + nextElement))
  return cumSum
}

export default {
  name: 'ColumnLayout',
  components: {
    ColumnOperations,
    DraggableContentNodes,
    ResizableColumn
  },
  mixins: [contentNodeMixin],
  data () {
    return {
      localColumnWidths: {}
    }
  },
  computed: {
    columns () {
      return keyBy(this.contentNode.data.columns || [], 'slot')
    },
    numColumns () {
      return this.contentNode.data.columns?.length || 0
    },
    lastColumn () {
      const slots = Object.keys(this.columns)
      return slots[slots.length - 1]
    },
    relativeColumnWidths () {
      // Cumulative sum of column widths, to know how many "width units" are to the left of each column
      // E.g. [0, 3, 8, 12] if there are three columns of width 3, 5, 4
      const cumSum = Object.values(this.localColumnWidths).reduce(cumulativeSumReducer, [0])
      // Map the cumulative sum values to the slot names
      // E.g. {'1': 0, '2': 3, '3': 8}
      const colsLeft = Object.fromEntries(Object.entries(this.localColumnWidths).map(([slot, width], idx) => [slot, cumSum[idx]]))
      // Also prepare the column width itself and the number of "width units" to the right of each column
      // E.g. {'1': [0, 3, 9], '2': [3, 5, 4], '3': [8, 4, 0]}
      return mapValues(this.localColumnWidths, (width, slot) => [colsLeft[slot], width, 12 - colsLeft[slot] - width])
    },
    color () {
      const h = (parseInt(this.contentNode.id, 16) % 360)
      return `hsl(${h}, 100%, 30%)`
    },
    isRoot () {
      return this.contentNode._meta.self === this.contentNode.root()._meta.self
    }
  },
  watch: {
    columns: {
      immediate: true,
      handler () {
        this.setLocalColumnWidths()
      }
    }
  },
  methods: {
    setLocalColumnWidths () {
      this.localColumnWidths = mapValues(this.columns, 'width')
    },
    resizeColumn (slot, width) {
      const oldWidth = this.localColumnWidths[slot]
      const diff = width - oldWidth
      const nextSlot = this.next(slot)
      this.localColumnWidths[slot] = width
      this.localColumnWidths[nextSlot] = this.localColumnWidths[nextSlot] - diff
    },
    next (slot) {
      const slots = Object.keys(this.columns)
      const index = slots.findIndex(s => s === slot)
      if (index === -1 || index === slots.length - 1) return undefined
      return slots[index + 1]
    },
    minWidth (slot) {
      return 3
    },
    maxWidth (slot) {
      const nextSlot = this.next(slot)
      if (nextSlot === undefined) return this.localColumnWidths[slot]
      return this.localColumnWidths[slot] + this.localColumnWidths[nextSlot] - this.minWidth(nextSlot)
    },
    async saveColumnWidths () {
      const payload = {
        data: {
          columns: this.contentNode.data.columns.map(column => ({
            ...column,
            width: this.localColumnWidths[column.slot]
          }))
        }
      }
      this.api.patch(this.contentNode, payload)
    }
  }
}
</script>

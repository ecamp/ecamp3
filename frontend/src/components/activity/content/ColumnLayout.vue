<template>
  <v-row v-if="!contentNode.loading" no-gutters>
    <resizable-column v-for="(column, slot) in columns"
                      :key="slot"
                      :width="localColumnWidths[slot] || 1"
                      :layout-mode="layoutMode"
                      :last="slot === lastColumn"
                      :min-width="minWidth(slot)"
                      :max-width="maxWidth(slot)"
                      @resizing="newWidth => resizeColumn(slot, newWidth)"
                      @resize-stop="saveColumnWidths">
      <draggable v-model="localColumnContents[slot]"
                 :disabled="!draggingEnabled"
                 group="contentNodes"
                 class="d-flex flex-column"
                 :class="{ 'column-min-height': layoutMode }"
                 @start="startDrag"
                 @end="finishDrag">
        <template v-if="layoutMode && $vuetify.breakpoint.smAndDown && numColumns > 1" #header>
          <v-subheader>
            {{ $tc('contentNode.columnLayout.entity.column.name') }}
            <v-progress-linear class="ml-3" buffer-value="100" :style="'flex-basis: ' + relativeColumnWidths[slot][0] + '0%'" />
            <v-progress-linear value="100" :style="'flex-basis: ' + relativeColumnWidths[slot][1] + '0%'" />
            <v-progress-linear buffer-value="100" :style="'flex-basis: ' + relativeColumnWidths[slot][2] + '0%'" />
          </v-subheader>
        </template>
        <content-node v-for="childNode in localColumnContents[slot]"
                      :key="childNode.id"
                      class="content-node"
                      :content-node="childNode"
                      :layout-mode="layoutMode"
                      :draggable="draggingEnabled" />
      </draggable>
      <v-row v-if="layoutMode"
             no-gutters
             justify="center"
             class="my-3">
        <v-menu bottom
                left
                offset-y>
          <template #activator="{ on, attrs }">
            <v-btn color="primary"
                   outlined
                   v-bind="attrs"
                   v-on="on">
              <v-icon left>mdi-plus-circle-outline</v-icon>
              {{ $tc('global.button.add') }}
            </v-btn>
          </template>
          <v-list>
            <v-list-item v-for="act in availableContentTypes"
                         :key="act.contentType.id"
                         @click="addContentNodeToSlot(act.contentType.id, column.slot)">
              <v-list-item-icon>
                <v-icon>{{ $tc(act.contentTypeIconKey) }}</v-icon>
              </v-list-item-icon>
              <v-list-item-title>
                {{ $tc(act.contentTypeNameKey) }}
              </v-list-item-title>
            </v-list-item>
          </v-list>
        </v-menu>
      </v-row>
    </resizable-column>
  </v-row>
</template>

<script>

import { groupBy, keyBy, sortBy, camelCase, mapValues } from 'lodash'
import { contentNodeMixin } from '@/mixins/contentNodeMixin.js'
import Draggable from 'vuedraggable'
import ResizableColumn from '@/components/activity/content/columnLayout/ResizableColumn.vue'

function cumulativeSumReducer (cumSum, nextElement) {
  cumSum.push((cumSum[cumSum.length - 1] + nextElement))
  return cumSum
}

export default {
  name: 'ColumnLayout',
  components: {
    // Lazy import necessary due to recursive component structure
    ContentNode: () => import('@/components/activity/ContentNode'),
    Draggable,
    ResizableColumn
  },
  mixins: [contentNodeMixin],
  data () {
    return {
      localColumnContents: {},
      localColumnWidths: {}
    }
  },
  computed: {
    draggingEnabled () {
      return this.layoutMode && this.$vuetify.breakpoint.mdAndUp
    },
    columns () {
      return keyBy(this.contentNode.jsonConfig?.columns || [], 'slot')
    },
    numColumns () {
      return this.contentNode.jsonConfig?.columns?.length || 0
    },
    lastColumn () {
      const slots = Object.keys(this.columns)
      return slots[slots.length - 1]
    },
    groupedChildren () {
      return groupBy(sortBy(this.contentNode.children().items, 'position'), 'slot')
    },
    columnContents () {
      return mapValues(this.columns, (_, slot) => this.groupedChildren[slot] || [])
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
    availableContentTypes () {
      return this.contentNode.ownerCategory().categoryContentTypes().items.map(cct => ({
        id: cct.id,
        contentType: cct.contentType(),
        contentTypeNameKey: 'contentNode.' + camelCase(cct.contentType().name) + '.name',
        contentTypeIconKey: 'contentNode.' + camelCase(cct.contentType().name) + '.icon'
      }))
    }
  },
  watch: {
    columns: {
      immediate: true,
      handler () {
        this.localColumnContents = this.columnContents
        this.setLocalColumnWidths()
      }
    },
    columnContents () {
      this.localColumnContents = this.columnContents
    }
  },
  mounted () {
    this.contentNode._meta.load.then(this.setLocalColumnWidths)
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
    startDrag () {
      document.body.classList.add('dragging')
    },
    finishDrag () {
      document.body.classList.remove('dragging')
      this.saveReorderedChildren()
    },
    async addContentNodeToSlot (contentTypeId, slot) {
      await this.api.post(await this.api.href(this.api.get(), 'contentNodes'), {
        parentId: this.contentNode.id,
        contentTypeId: contentTypeId,
        slot: slot
      })
      this.api.reload(this.contentNode)
    },
    async saveReorderedChildren () {
      const payload = Object.fromEntries(Object.entries(this.localColumnContents).flatMap(([slot, columnContents]) => {
        let position = 0
        return columnContents.map(contentNode => {
          return [contentNode.id, {
            slot: slot,
            position: position++
          }]
        })
      }))
      this.api.patch(await this.api.href(this.contentNode, 'children'), payload)
    },
    async saveColumnWidths () {
      const payload = {
        jsonConfig: {
          ...this.contentNode.jsonConfig,
          columns: this.contentNode.jsonConfig.columns.map(column => ({
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

<style scoped>
.column-min-height {
  min-height: 10rem;
}
</style>
<style lang="scss">
.resizable-col {
  .content-node {
    border-bottom: 1px solid rgba(0, 0, 0, 0.12);
    border-radius: 0;
  }

  @media #{map-get($display-breakpoints, 'sm-and-down')} {
    border-bottom: 1px solid rgba(0, 0, 0, 0.32);
  }

  &:not(.layout-mode) {
    @media #{map-get($display-breakpoints, 'md-and-up')} {
      &+.resizable-col:not(.layout-mode) {
        border-left: 1px solid rgba(0, 0, 0, 0.12);
      }
    }
  }
}

</style>

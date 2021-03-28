<template>
  <div v-if="!contentNode.loading" :class="{ 'd-flex': $vuetify.breakpoint.mdAndUp }">
    <resizable-column v-for="(column, slot) in columns"
                      :key="slot"
                      :width="localColumnWidths[slot] || 1"
                      :layout-mode="layoutMode"
                      :last="slot === lastColumn"
                      :max-width="maxWidth(slot)"
                      @resizing="newWidth => resizeColumn(slot, newWidth)"
                      @resize-stop="saveColumnWidths">
      <draggable v-model="localColumnContents[slot]"
                 :disabled="!layoutMode"
                 group="contentNodes"
                 class="d-flex flex-column"
                 :class="{ 'column-min-height': layoutMode }"
                 @end="saveReorderedChildren">
        <content-node v-for="childNode in localColumnContents[slot]"
                      :key="childNode.id"
                      :content-node="childNode"
                      :layout-mode="layoutMode" />
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
  </div>
</template>

<script>

import { groupBy, keyBy, sortBy, camelCase, mapValues } from 'lodash'
import { contentNodeMixin } from '@/mixins/contentNodeMixin'
import Draggable from 'vuedraggable'
import ResizableColumn from '@/components/activity/content/columnLayout/ResizableColumn'

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
      localColumnWidths: []
    }
  },
  computed: {
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
    maxWidth (slot) {
      const nextSlot = this.next(slot)
      if (nextSlot === undefined) return this.localColumnWidths[slot]
      return this.localColumnWidths[slot] + this.localColumnWidths[nextSlot] - 1
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
            parentId: this.contentNode.id,
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

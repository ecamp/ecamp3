<template>
  <v-container fluid class="px-0 py-0">
    <v-row>
      <v-col v-for="(column, idx) in columns"
             :key="idx"
             cols="12"
             class="col-md">
        <draggable v-model="localColumnContents[column.slot]"
                   :disabled="!layoutMode"
                   group="contentNodes"
                   class="d-flex flex-column"
                   :class="{ 'column-min-height': layoutMode }"
                   @end="saveReorderedChildren">
          <content-node v-for="childNode in localColumnContents[column.slot]"
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
      </v-col>
    </v-row>
  </v-container>
</template>

<script>

import { groupBy, sortBy, merge, camelCase } from 'lodash'
import { contentNodeMixin } from '@/mixins/contentNodeMixin'
import Draggable from 'vuedraggable'

export default {
  name: 'ColumnLayout',
  components: {
    // Lazy import necessary due to recursive component structure
    ContentNode: () => import('@/components/activity/ContentNode'),
    Draggable
  },
  mixins: [contentNodeMixin],
  data () {
    return {
      localColumnContents: {}
    }
  },
  computed: {
    columns () {
      return this.contentNode.jsonConfig?.columns || []
    },
    emptyColumns () {
      return Object.fromEntries(this.columns.map(column => ([column?.slot, []])))
    },
    columnContents () {
      return merge({}, this.emptyColumns, groupBy(sortBy(this.contentNode.children().items, 'position'), 'slot'))
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
    columnContents: {
      immediate: true,
      handler () {
        this.localColumnContents = this.columnContents
      }
    }
  },
  methods: {
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
    }
  }
}
</script>

<style scoped>
.column-min-height {
  min-height: 4rem;
}
</style>

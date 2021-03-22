<template>
  <v-container fluid class="px-0 py-0">
    <v-row class="mx-0 my-0">
      <v-col v-for="(column, idx) in columns"
             :key="idx"
             cols="12"
             class="col-md">
        <content-node v-for="childNode in columnContents[column.slot]"
                      :key="childNode.id"
                      :content-node="childNode"
                      :layout-mode="layoutMode" />
        <v-row no-gutters justify="center" class="my-3">
          <v-menu v-if="layoutMode"
                  bottom
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

import { groupBy, sortBy, camelCase } from 'lodash'
import { contentNodeMixin } from '@/mixins/contentNodeMixin'

export default {
  name: 'ColumnLayout',
  components: {
    // Lazy import necessary due to recursive component structure
    ContentNode: () => import('@/components/activity/ContentNode')
  },
  mixins: [contentNodeMixin],
  computed: {
    columns () {
      return this.contentNode.jsonConfig?.columns || []
    },
    columnContents () {
      return groupBy(sortBy(this.contentNode.children().items, 'position'), 'slot')
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
  methods: {
    async addContentNodeToSlot (contentTypeId, slot) {
      await this.api.post('/content-nodes', {
        parentId: this.contentNode.id,
        contentTypeId: contentTypeId,
        slot: slot
      })
      this.api.reload(this.contentNode)
    }
  }
}
</script>

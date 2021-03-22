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
  props: {
    contentNode: { type: Object, required: true }
  },
  computed: {
    columns () {
      return this.contentNode.jsonConfig?.columns || []
    },
    columnContents () {
      return groupBy(sortBy(this.contentNode.children().items, 'position'), 'slot')
    }
  }
}
</script>

<template>
  <div class="d-md-flex">
    <div v-for="(column, idx) in columns" :key="idx" class="flex-md-grow-1 mx-md-1">
      <content-node v-for="childNode in columnContents[column.slot]"
                    :key="childNode.id"
                    :content-node="childNode" />
    </div>
  </div>
</template>

<script>

import { groupBy, sortBy } from 'lodash'

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

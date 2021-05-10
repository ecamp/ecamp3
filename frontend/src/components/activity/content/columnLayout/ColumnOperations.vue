<template>
  <v-list-item :disabled="!addingColumnEnabled" @click="addColumn">
    <v-list-item-icon>
      <v-icon>mdi-columns-plus</v-icon>
    </v-list-item-icon>
    <v-list-item-title>
      {{ $tc('components.activity.content.columnLayout.resizableColumn.addColumn') }}
    </v-list-item-title>
  </v-list-item>
</template>
<script>
import { cloneDeep } from 'lodash'
import { calculateNextSlotName, limitColumnWidths } from '@/components/activity/content/columnLayout/calculateNextSlotName.js'

export default {
  name: 'ColumnOperations',
  props: {
    contentNode: { type: Object, required: true },
    minColumnWidth: { type: Number, default: 3 },
    totalWidth: { type: Number, default: 12 }
  },
  computed: {
    addingColumnEnabled () {
      return (this.contentNode.jsonConfig.columns.length + 1) * this.minColumnWidth <= this.totalWidth
    }
  },
  methods: {
    addColumn () {
      const jsonConfig = cloneDeep(this.contentNode.jsonConfig)
      const newSlotName = calculateNextSlotName(jsonConfig.columns.map(col => col.slot))
      jsonConfig.columns.push({
        slot: newSlotName,
        width: this.minColumnWidth
      })
      console.log(jsonConfig.columns)
      jsonConfig.columns = limitColumnWidths(jsonConfig.columns, this.minColumnWidth, this.totalWidth)
      console.log(jsonConfig.columns)
      this.contentNode.$patch({ jsonConfig })
    }
  }
}
</script>

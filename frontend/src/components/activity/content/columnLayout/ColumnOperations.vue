<template>
  <div>
    <v-list-item :disabled="!addingColumnEnabled" @click="addColumn">
      <v-list-item-icon>
        <v-icon>mdi-playlist-plus</v-icon>
      </v-list-item-icon>
      <v-list-item-title>
        {{ $tc('components.activity.content.columnLayout.resizableColumn.addColumn') }}
      </v-list-item-title>
    </v-list-item>
    <v-list-item :disabled="!removingColumnEnabled" @click="removeColumn">
      <v-list-item-icon>
        <v-icon>mdi-playlist-minus</v-icon>
      </v-list-item-icon>
      <v-list-item-title>
        {{ $tc('components.activity.content.columnLayout.resizableColumn.removeColumn') }}
      </v-list-item-title>
    </v-list-item>
  </div>
</template>
<script>
import { cloneDeep, groupBy } from 'lodash'
import { calculateNextSlotName, adjustColumnWidths } from '@/components/activity/content/columnLayout/calculateNextSlotName.js'

export default {
  name: 'ColumnOperations',
  inject: ['allContentNodes'],
  props: {
    contentNode: { type: Object, required: true },
    minColumnWidth: { type: Number, default: 3 },
    totalWidth: { type: Number, default: 12 }
  },
  computed: {
    addingColumnEnabled () {
      return (this.contentNode.data.columns.length + 1) * this.minColumnWidth <= this.totalWidth
    },
    removingColumnEnabled () {
      return this.contentNode.data.columns.length > 2 && this.removableColumn !== undefined
    },
    children () {
      return this.allContentNodes().items.filter(child => {
        return child.parent !== null && child.parent()._meta.self === this.contentNode._meta.self
      })
    },
    childrenBySlot () {
      return groupBy(this.children, 'slot')
    },
    removableColumn () {
      return this.contentNode.data.columns.map(col => col.slot).reverse().find(slot => {
        return !Object.keys(this.childrenBySlot).includes(slot)
      })
    }
  },
  methods: {
    addColumn () {
      let columns = cloneDeep(this.contentNode.data.columns)
      const newSlotName = calculateNextSlotName(columns.map(col => col.slot))
      columns.push({
        slot: newSlotName,
        width: this.minColumnWidth
      })
      columns = adjustColumnWidths(columns, this.minColumnWidth, this.totalWidth)
      this.contentNode.$patch({ data: { columns } })
    },
    removeColumn () {
      let columns = cloneDeep(this.contentNode.data.columns)
      columns = adjustColumnWidths(columns.filter(col => col.slot !== this.removableColumn), this.minColumnWidth, this.totalWidth)
      this.contentNode.$patch({ data: { columns } })
    }
  }
}
</script>

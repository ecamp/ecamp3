<template>
  <div :style="columnStyles" :class="widthClass" class="tw-align-top">
    <ContentNodeComponent
      v-for="child in children"
      :key="child.id"
      :content-node="child"
    />
  </div>
</template>

<script setup>
import ContentNodeComponent from './ContentNode.vue'
</script>

<script>
export default defineNuxtComponent({
  props: {
    contentNode: { type: Object, required: true },
    columnSlot: { type: String, required: true },
  },
  computed: {
    column() {
      return this.contentNode.data.columns.find(
        (column) => column.slot === this.columnSlot
      )
    },
    widthClass() {
      return 'ec-col ec-col-' + this.column.width
    },
    columnStyles() {
      return {
        borderLeft: this.columnSlot === this.firstSlot ? 'none' : '1px solid black',
        padding:
          '4px ' +
          (this.columnSlot === this.lastSlot ? '0' : '1%') +
          ' 4px ' +
          (this.columnSlot === this.firstSlot ? '0' : '1%'),
      }
    },
    firstSlot() {
      return this.contentNode.data.columns[0].slot
    },
    lastSlot() {
      return this.contentNode.data.columns[this.contentNode.data.columns.length - 1].slot
    },
    children() {
      return this.contentNode
        .children()
        .items.filter((child) => child.slot === this.columnSlot)
        .sort((child1, child2) => parseInt(child1.position) - parseInt(child2.position))
    },
  },
})
</script>

<style lang="scss" scoped>
.ec-col {
  flex: 0 0 100%;
}

@for $i from 1 through 12 {
  .ec-col-#{$i} {
    flex: $i 0 0;
  }
}
</style>

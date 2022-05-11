<template>
  <td :style="columnStyles" class="tw-align-top">
    <content-node
      v-for="child in children"
      :key="child.id"
      :content-node="child"
    />
  </td>
</template>

<script>
export default {
  components: { ContentNode: () => import('./ContentNode.vue') },
  props: {
    contentNode: { type: Object, required: true },
    columnSlot: { type: String, required: true },
  },
  computed: {
    column() {
      return this.contentNode.columns.find(
        (column) => column.slot === this.columnSlot
      )
    },
    width() {
      return this.column.width
    },
    columnStyles() {
      return {
        width: (this.width / 12.0) * 100.0 + '%',
        borderLeft:
          this.columnSlot === this.firstSlot ? 'none' : '1px solid black',
        padding:
          '4px ' +
          (this.columnSlot === this.lastSlot ? '0' : '1%') +
          ' 4px ' +
          (this.columnSlot === this.firstSlot ? '0' : '1%'),
      }
    },
    firstSlot() {
      return this.contentNode.columns[0].slot
    },
    lastSlot() {
      return this.contentNode.columns[this.contentNode.columns.length - 1].slot
    },
    children() {
      return this.contentNode
        .children()
        .items.filter((child) => child.slot === this.columnSlot)
        .sort(
          (child1, child2) =>
            parseInt(child1.position) - parseInt(child2.position)
        )
    },
  },
}
</script>

<template>
  <div class="wrapper">
    <column-layout-column
      v-for="column in contentNode.columns"
      :key="column.slot"
      :content-node="contentNode"
      :column-slot="column.slot"
    />
  </div>
</template>

<script>
import ColumnLayoutColumn from './ColumnLayoutColumn.vue'
export default {
  components: { ColumnLayoutColumn },
  props: {
    contentNode: { type: Object, required: true },
  },
}
</script>

<style scoped lang="scss">
.wrapper {
  display: flex;
  flex-direction: row;
}
</style>

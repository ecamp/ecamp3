<template>
  <table class="tw-w-full">
    <tr>
      <column-layout-column
        v-for="column in contentNode.columns"
        :key="column.slot"
        :content-node="contentNode"
        :column-slot="column.slot"
      />
    </tr>
  </table>
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

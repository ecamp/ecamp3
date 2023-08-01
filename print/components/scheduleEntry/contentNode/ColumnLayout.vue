<template>
  <div class="tw-flex tw-flex-wrap">
    <column-layout-column
      v-for="column in contentNode.data.columns"
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

<template>
  <v-expansion-panel>
    <v-expansion-panel-title>
      <h3 class="text-grey-darken-1">
        {{ period.description }}
      </h3>
    </v-expansion-panel-title>
    <v-expansion-panel-text>
      <material-table
        :camp="camp"
        :material-item-collection="materialItemCollection"
        :material-list="materialList"
        :period="period"
        :disabled="disabled"
      />
    </v-expansion-panel-text>
  </v-expansion-panel>
</template>

<script>
import MaterialTable from '@/components/material/MaterialTable.vue'

export default {
  name: 'PeriodMaterialLists',
  components: {
    MaterialTable,
  },
  props: {
    period: { type: Object, required: true },
    materialList: { type: Object, required: false, default: null },
    materialItemCollection: { type: Object, required: true },
    disabled: { type: Boolean, default: false },
  },
  computed: {
    camp() {
      return this.period.camp()
    },
  },
}
</script>

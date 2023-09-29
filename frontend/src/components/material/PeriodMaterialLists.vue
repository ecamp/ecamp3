<template>
  <v-expansion-panel>
    <v-expansion-panel-header>
      <h3 class="grey--text text--darken-1">
        {{ period.description }}
      </h3>
    </v-expansion-panel-header>
    <v-expansion-panel-content>
      <material-table
        :camp="camp"
        :material-item-collection="materialItemCollection"
        :material-list="materialList"
        :period="period"
        :disabled="disabled"
      />
    </v-expansion-panel-content>
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

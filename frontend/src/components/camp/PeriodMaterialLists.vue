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
        :material-item-collection="period.materialItems()"
        :period="period"
        :show-content-node-material="showContentNodeMaterial"
        :group-by-list="groupByList"
        enable-grouping
        :disabled="disabled" />
    </v-expansion-panel-content>
  </v-expansion-panel>
</template>

<script>

import MaterialTable from '@/components/material/MaterialTable.vue'

export default {
  name: 'PeriodMaterialLists',
  components: {
    MaterialTable
  },
  props: {
    period: { type: Object, required: true },
    showContentNodeMaterial: { type: Boolean, required: true },
    groupByList: { type: Boolean, required: true },
    disabled: { type: Boolean, default: false }
  },
  data () {
    return {
      newMaterialItems: {}
    }
  },
  computed: {
    camp () {
      return this.period.camp()
    }
  },
  // reload data every time user navigates to material table
  mounted () {
    this.period.materialItems().$reload()
  }
}

</script>

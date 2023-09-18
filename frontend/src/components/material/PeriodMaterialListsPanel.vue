<template>
  <v-expansion-panel :value="true">
    <v-expansion-panel-header>
      <h3 class="grey--text text--darken-1">
        {{ period.description }}
      </h3>
    </v-expansion-panel-header>
    <v-expansion-panel-content :class="{ 'px-0': $vuetify.breakpoint.xsOnly }">
      <MaterialTable
        :camp="camp"
        :material-item-collection="materialItems"
        :material-list="materialList"
        :period="period"
        :group-by-list="groupByList"
        enable-grouping
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
    camp: { type: Object, required: true },
    materialList: { type: Object, required: true },
    period: { type: Object, required: true },
    groupByList: { type: Boolean, default: false },
    disabled: { type: Boolean, default: false },
  },
  data() {
    return {
      newMaterialItems: {},
    }
  },
  computed: {
    materialItems() {
      return this.api.get().materialItems({
        period: this.period._meta.self,
        materialList: this.materialList._meta.self,
      })
    },
  },
  // reload data every time user navigates to material table
  mounted() {
    this.period.materialItems().$reload()
  },
}
</script>

<style scoped lang="scss">
@media #{map-get($display-breakpoints, 'xs-only')} {
  ::v-deep .v-expansion-panel-content__wrap {
    padding-left: 0;
    padding-right: 0;
  }
}
</style>

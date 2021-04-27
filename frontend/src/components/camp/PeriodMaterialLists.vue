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
        enable-grouping />
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
    showContentNodeMaterial: { type: Boolean, required: true }
  },
  data () {
    return {
      newMaterialItems: {}
    }
  },
  computed: {
    camp () {
      return this.period.camp()
    },
    materialLists () {
      return this.camp.materialLists()
    },
    materialListsDetails () {
      return this.camp.materialLists().items.map(l => ({
        id: l.id,
        list: l,
        items: this.getMaterialItems(l)
      }))
    },
    materialListOptions () {
      return this.camp.materialLists().items.map(l => ({
        value: l.id,
        text: l.name
      }))
    }
  },
  methods: {
    getMaterialItems (materialList) {
      const items = []

      // Period-Material
      materialList.materialItems().items
        .filter(mi => mi.period !== null)
        .filter(mi => mi.period().id === this.period.id)
        .forEach(mi => items.push({
          key: mi.id,
          materialItem: mi,
          period: mi.period(),
          scheduleEntry: null
        }))

      // eager add new Items
      for (const key in this.newMaterialItems) {
        const mi = this.newMaterialItems[key]
        if (mi.materialListId === materialList.id) {
          items.push({
            key: key,
            materialItem: mi,
            period: null,
            scheduleEntry: null
          })
        }
      }

      // ContentNode-Material
      if (this.showContentNodeMaterial) {
        materialList.materialItems().items
          .filter(mi => mi.contentNode !== null)
          .forEach(mi => mi.contentNode().owner().scheduleEntries().items
            .filter(se => se.period().id === this.period.id)
            .forEach(se => items.push({
              key: mi.id + '/' + se.id,
              materialItem: mi,
              period: null,
              scheduleEntry: se
            }))
          )
      }

      return items.sort((a, b) => {
        if (a.materialItem.article === b.materialItem.article) {
          if (a.period !== null) {
            return 1
          } else if (b.period !== null) {
            return -1
          } else {
            return a.scheduleEntry.periodOffset - b.scheduleEntry.periodOffset
          }
        } else {
          return a.materialItem.article.localeCompare(b.materialItem.article)
        }
      })
    },
    onItemAdding (key, data, res) {
      this.$set(this.newMaterialItems, key, data)

      res.then(mi => {
        this.api.reload(mi.materialList().materialItems()).then(() => {
          this.$delete(this.newMaterialItems, key)
        })
      })
    }
  }
}

</script>

<style scoped>
  .text-align-right {
    text-align: right;
    padding-right: 9px !important;
  }
  .font-size-16 {
    font-size: 16px !important;
  }
  .v-data-table >>> .v-data-table__wrapper > table > thead > tr > th {
    padding: 0 2px;
  }
  .v-data-table >>> .v-data-table__wrapper > table > tbody > tr > td {
    padding: 0 2px;
  }
</style>

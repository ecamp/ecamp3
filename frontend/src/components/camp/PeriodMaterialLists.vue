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
        :show-content-node-material="showContentNodeMaterial" />

      <!--
      <div v-for="materialListDetail in materialListsDetails"
           :key="materialListDetail.id">
        <div v-if="materialListDetail.items.length > 0">
          <h2 style="padding: 0 0 6px 0">
            {{ materialListDetail.list.name }}
          </h2>
          <v-simple-table dense style="padding-bottom: 24px">
            <colgroup>
              <col style="width: 55px;">
              <col style="width: 15%;">
              <col>
              <col style="width: 20%;">
            </colgroup>
            <thead>
              <tr>
                <th colspan="2">
                  {{ $tc('entity.materialItem.fields.quantity') }}
                </th>
                <th>
                  {{ $tc('entity.materialItem.fields.article') }}
                </th>
                <th>
                  Option
                </th>
              </tr>
            </thead>
            <tbody>
              <template v-for="item in materialListDetail.items">
                <material-list-item-content-node
                  v-if="item.scheduleEntry != null"
                  :key="item.key"
                  :camp="camp"
                  :item="item" />
                <material-list-item-period
                  v-else-if="item.period != null"
                  :key="item.key"
                  :item="item" />
                <tr v-else :key="item.key">
                  <td class="font-size-16 text-align-right">
                    {{ item.materialItem.quantity }}
                  </td>
                  <td class="font-size-16">
                    {{ item.materialItem.unit }}
                  </td>
                  <td class="font-size-16">
                    {{ item.materialItem.article }}
                  </td>
                  <td>
                    <v-progress-circular
                      indeterminate
                      color="primary" />
                  </td>
                </tr>
              </template>
            </tbody>
          </v-simple-table>
        </div>
      </div>

      <material-create-item
        v-if="$vuetify.breakpoint.smAndUp"
        :camp="camp"
        :period="period"
        @item-adding="onItemAdding" />

      <div v-else style="margin-top: 20px; text-align: right">
        <dialog-material-item-create :camp="camp" :period="period">
          <template #activator="{ on }">
            <v-btn color="success" v-on="on">
              {{ $tc('components.camp.periodMaterialLists.addNewItem') }}
            </v-btn>
          </template>
        </dialog-material-item-create>
      </div> -->
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

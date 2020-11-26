<template>
  <v-expansion-panel>
    <v-expansion-panel-header>
      <h3 class="grey--text text--darken-1">
        {{ period.description }}
      </h3>
    </v-expansion-panel-header>
    <v-expansion-panel-content>
      <div v-for="materialListDetail in materialListsDetails"
           :key="materialListDetail.id">
        <div v-if="materialListDetail.items.length > 0">
          <h2>{{ materialListDetail.list.name }}</h2>
          <v-simple-table dense>
            <thead>
              <tr>
                <th class="text-left" style="width: 10%;">
                  {{ $tc('entity.materialItem.fields.quantity') }}
                </th>
                <th style="width: 15%" />
                <th class="text-left">
                  {{ $tc('entity.materialItem.fields.article') }}
                </th>
                <th class="text-left" style="width: 20%;">
                  {{ $tc('entity.activity.name') }}
                </th>
                <th class="text-left" style="width: 15%;">
                  Option
                </th>
              </tr>
            </thead>
            <tbody>
              <template v-for="item in materialListDetail.items">
                <material-list-item-activity
                  v-if="item.scheduleEntry != null"
                  :key="item.key"
                  :camp="camp"
                  :item="item" />
                <material-list-item-period
                  v-else-if="item.period != null"
                  :key="item.key"
                  :item="item" />
                <tr v-else :key="item.key">
                  <td>{{ item.materialItem.quantity }}</td>
                  <td>{{ item.materialItem.unit }}</td>
                  <td>{{ item.materialItem.article }}</td>
                  <td />
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
        :camp="camp"
        :period="period"
        @item-adding="onItemAdding" />
    </v-expansion-panel-content>
  </v-expansion-panel>
</template>

<script>
import MaterialCreateItem from './MaterialCreateItem.vue'
import MaterialListItemActivity from './MaterialListItemActivity'
import MaterialListItemPeriod from './MaterialListItemPeriod'

export default {
  name: 'PeriodMaterialLists',
  components: {
    MaterialCreateItem,
    MaterialListItemActivity,
    MaterialListItemPeriod
  },
  props: {
    period: { type: Object, required: true },
    showActivityMaterial: { type: Boolean, required: true }
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
      for (var key in this.newMaterialItems) {
        var mi = this.newMaterialItems[key]
        if (mi.materialListId === materialList.id) {
          items.push({
            key: key,
            materialItem: mi,
            period: null,
            scheduleEntry: null
          })
        }
      }

      // Activity-Material
      if (this.showActivityMaterial) {
        materialList.materialItems().items
          .filter(mi => mi.activityContent !== null)
          .forEach(mi => mi.activityContent().activity().scheduleEntries().items
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
</style>

<template>
  <v-expansion-panel>
    <v-expansion-panel-header>
      <h3 class="grey--text text--darken-1">
        {{ period.description }}
      </h3>
    </v-expansion-panel-header>
    <v-expansion-panel-content>
      <div v-for="materialList in materialLists.items"
           :key="materialList.id"
           :set="materialItems = getMaterialItems(period, materialList)">
        <div v-if="materialItems.length > 0">
          <h2>{{ materialList.name }}</h2>
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
              <template v-for="item in materialItems">
                <material-list-item-period
                  v-if="item.scheduleEntry == null"
                  :key="item.key"
                  :item="item" />
                <material-list-item-activity
                  v-else
                  :key="item.key"
                  :camp="camp"
                  :item="item" />
              </template>
            </tbody>
          </v-simple-table>
        </div>
      </div>

      <material-create-item :camp="camp" :period="period" @item-add="onItemAdd" />
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
    }
  },
  computed: {
    camp () {
      return this.period.camp()
    },
    materialLists () {
      return this.camp.materialLists()
    },
    materialListOptions () {
      return this.camp.materialLists().items.map(l => ({
        value: l.id,
        text: l.name
      }))
    }
  },
  methods: {
    getMaterialItems (period, materialList) {
      const items = []

      // Period-Material
      materialList.materialItems().items
        .filter(mi => mi.period !== null)
        .filter(mi => mi.period().id === period.id)
        .forEach(mi => items.push({
          key: mi.id,
          materialItem: mi,
          period: mi.period(),
          scheduleEntry: null
        }))

      // Activity-Material
      if (this.showActivityMaterial) {
        materialList.materialItems().items
          .filter(mi => mi.activityContent !== null)
          .forEach(mi => mi.activityContent().activity().scheduleEntries().items
            .filter(se => se.period().id === period.id)
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
    onItemAdd (mi) {
      this.api.reload(mi.materialList().materialItems())
    }
  }
}

</script>

<style scoped>
</style>

<template>
  <v-expansion-panel>
    <v-expansion-panel-header>
      {{ period.description }}
    </v-expansion-panel-header>
    <v-expansion-panel-content>
      <div v-for="materialList in materialLists.items"
           :key="materialList.id">
        <h2>{{ materialList.name }}</h2>
        <v-simple-table dense>
          <thead>
            <tr>
              <th class="text-left">
                Anzahl
              </th>
              <th class="text-left">
                Artikel
              </th>
              <th class="text-left">
                Block
              </th>
              <th class="text-left">
                Option
              </th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="item in getMaterialItems(period, materialList)"
                :key="item.key">
              <td>
                {{ item.materialItem.quantity }}
                {{ item.materialItem.unit }}
              </td>
              <td>{{ item.materialItem.article }}</td>
              <td>
                <router-link v-if="item.scheduleEntry !== null"
                             :to="scheduleEntryRoute(camp, item.scheduleEntry)">
                  {{ item.scheduleEntry.number }}:
                  {{ item.scheduleEntry.activity().title }}
                </router-link>
              </td>
              <td>
                <a v-if="item.scheduleEntry == null"
                   href="#" @click="deleteMaterialItem(item.materialItem)">
                  delete
                </a>
              </td>
            </tr>
          </tbody>
        </v-simple-table>
      </div>

      <material-create-item :camp="camp" :period="period" @item-add="onItemAdd" />
    </v-expansion-panel-content>
  </v-expansion-panel>
</template>

<script>
import { scheduleEntryRoute } from '@/router'
import MaterialCreateItem from './MaterialCreateItem.vue'

export default {
  name: 'MaterialLists',
  components: { MaterialCreateItem },
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
    }
  },
  methods: {
    scheduleEntryRoute,
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
    deleteMaterialItem (materialItem) {
      this.api.del(materialItem)
    },
    onItemAdd (mi) {
      this.api.reload(mi.materialList().materialItems())
    }
  }
}

</script>

<style scoped>
</style>

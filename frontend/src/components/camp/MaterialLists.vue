<template>
  <div>
    <div v-for="period in periods.items" :key="period.id">
      <h1>{{ period.description }}</h1>

      <div v-for="materialList in materialLists.items" :key="materialList.id">
        <h2>{{ materialList.name }}</h2>

        <ul>
          <li v-for="item in getMaterialItems(period, materialList)"
              :key="item.key">
            {{ item.materialItem.article }}
            {{ item.materialItem.quantity }}
            {{ item.materialItem.unit }}
            -
            <router-link v-if="item.scheduleEntry !== null"
                         :to="scheduleEntryRoute(camp(), item.scheduleEntry)">
              {{ item.scheduleEntry.number }}:
              {{ item.scheduleEntry.activity().title }}
            </router-link>

            <a v-else href="#" @click="deleteMaterialItem(item.materialItem)">
              delete
            </a>
          </li>
        </ul>
      </div>
      <material-create-item :camp="camp()" :period="period" @item-add="onItemAdd" />
    </div>
  </div>
</template>

<script>
import { scheduleEntryRoute } from '@/router'
import MaterialCreateItem from './MaterialCreateItem.vue'

export default {
  name: 'MaterialLists',
  components: { MaterialCreateItem },
  props: {
    camp: { type: Function, required: true }
  },
  data () {
    return {
    }
  },
  computed: {
    periods () {
      return this.camp().periods()
    },
    materialLists () {
      return this.camp().materialLists()
    }
  },
  methods: {
    scheduleEntryRoute,
    getMaterialItems (period, materialList) {
      const items = []

      materialList.materialItems().items
        .filter(mi => mi.period !== null)
        .filter(mi => mi.period().id === period.id)
        .forEach(mi => items.push({
          key: mi.id,
          materialItem: mi,
          period: mi.period(),
          scheduleEntry: null
        }))

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

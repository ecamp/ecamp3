<template>
  <side-bar>
    <content-card>
      <v-list-item
        :to="campRoute(camp(), 'material')"
        exact
          color="secondary"
        class="text-uppercase subtitle-2"
      >
        {{ $tc('views.activity.sideBarMateriallists.title') }}
      </v-list-item>
      <v-list>
        <v-list-item
          v-for="materialList in materialLists.allItems"
          :key="materialList._meta.self"
          :to="materialRoute(camp(), materialList)"
        >
          <v-list-item-content>
            <v-list-item-title>{{ materialList.name }}</v-list-item-title>
            <v-list-item-subtitle
              >{{
                $tc(
                  'components.campAdmin.campMaterialListsItem.materialsCount',
                  materialList.materialItems().totalItems,
                  {
                    count: materialList.materialItems().totalItems,
                  }
                )
              }}
            </v-list-item-subtitle>
          </v-list-item-content>
        </v-list-item>
      </v-list>
    </content-card>
  </side-bar>
</template>

<script>
import SideBar from '@/components/navigation/SideBar.vue'
import ContentCard from '@/components/layout/ContentCard.vue'
import { campRoute, materialRoute } from '@/router.js'

import { HTML5_FMT } from '@/common/helpers/dateFormat.js'

export default {
  name: 'SideBarMateriallists',
  components: { ContentCard, SideBar },
  props: {
    camp: { type: Function, required: true },
  },
  computed: {
    materialLists() {
      return this.camp().materialLists()
    },
    period() {
      return this.day().period
    },
    currentDayAsString() {
      return this.$date.utc(this.day().start).format(HTML5_FMT.DATE)
    },
  },
  methods: {
    campRoute,
    materialRoute,
  },
}
</script>

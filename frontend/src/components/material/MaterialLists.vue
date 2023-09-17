<template>
  <v-list>
    <v-list-item :to="materialRoute(camp(), false, { isDetail: true })" exact-path>
      <v-list-item-content>
        {{ $tc('components.material.materialLists.overview') }}
      </v-list-item-content>
      <v-list-item-icon>
        <v-icon color="blue-grey lighten-3">mdi-chevron-right</v-icon>
      </v-list-item-icon>
    </v-list-item>
    <v-skeleton-loader v-if="materialLists._meta.loading" type="list-item@3" />
    <v-list-item
      v-for="materialList in materialLists.allItems"
      :key="materialList._meta.self"
      :to="materialRoute(camp(), materialList, { isDetail: true })"
      exact-path
    >
      <v-list-item-content>
        <v-list-item-title>{{ materialList.name }}</v-list-item-title>
        <v-list-item-subtitle
          >{{
            $tc(
              'components.material.materialLists.materialsCount',
              materialList.itemCount,
              {
                count: materialList.itemCount,
              }
            )
          }}
        </v-list-item-subtitle>
      </v-list-item-content>
      <v-list-item-icon>
        <v-icon color="blue-grey lighten-3">mdi-chevron-right</v-icon>
      </v-list-item-icon>
    </v-list-item>
  </v-list>
</template>

<script>
import { campRoute, materialRoute } from '@/router.js'

export default {
  name: 'MaterialLists',
  props: {
    camp: { type: Function, required: true },
  },
  computed: {
    materialLists() {
      return this.camp().materialLists()
    },
  },
  methods: {
    campRoute,
    materialRoute,
  },
}
</script>

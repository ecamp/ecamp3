<template>
  <SideBar
    :title="$tc('views.camp.category.sideBarCategory.title')"
    icon="mdi-format-list-numbered"
  >
    <v-list>
      <v-list-item
        v-for="category in camp.categories().items"
        :key="category._meta.self"
        :to="categoryRoute(camp, category)"
        replace
      >
        <v-list-item-title class="d-flex gap-2 align-baseline">
          <CategoryChip
            :category="category"
            class="flex-shrink-0 pointer-events-none"
            dense
          />
          <span class="font-weight-medium">{{ category.name }}</span>
        </v-list-item-title>
      </v-list-item>
    </v-list>
  </SideBar>
</template>

<script>
import SideBar from '@/components/navigation/SideBar.vue'
import CategoryChip from '@/components/generic/CategoryChip.vue'
import { categoryRoute } from '@/router.js'
export default {
  name: 'SideBarCategory',
  components: { CategoryChip, SideBar },
  props: {
    camp: { type: Object, required: true },
  },
  methods: { categoryRoute },
}
</script>

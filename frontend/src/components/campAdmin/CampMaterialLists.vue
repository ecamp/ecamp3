<template>
  <v-skeleton-loader v-if="camp().materialLists()._meta.loading" type="article" />
  <v-list v-else class="py-0">
    <template
      v-for="materialList in materialLists.items">
      <camp-material-lists-item
        :key="materialList._meta.self"
        :material-list="materialList"
        :disabled="disabled" />
      <v-divider
        :key="materialList._meta.self + 'div'" />
    </template>
  </v-list>
</template>

<script>
import CampMaterialListsItem from '@/components/campAdmin/CampMaterialListsItem.vue'

export default {
  name: 'CampMaterialLists',
  components: {
    CampMaterialListsItem
  },
  props: {
    camp: {
      type: Function,
      required: true
    },
    disabled: {
      type: Boolean,
      default: false
    }
  },
  data () {
    return {}
  },
  computed: {
    materialLists () {
      return this.camp().materialLists()
    }
  }
}
</script>

<style scoped>
</style>

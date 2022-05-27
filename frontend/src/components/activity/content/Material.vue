<template>
  <card-content-node v-bind="$props">
    <div class="mb-3">
      <material-table :camp="camp()"
                      :material-node="contentNode"
                      :layout-mode="layoutMode"
                      :material-item-collection="materialItemCollection"
                      :group-by-list="$vuetify.breakpoint.xs"
                      :disabled="disabled" />
    </div>
  </card-content-node>
</template>

<script>

import CardContentNode from '@/components/activity/CardContentNode.vue'
import { contentNodeMixin } from '@/mixins/contentNodeMixin.js'
import MaterialTable from '@/components/material/MaterialTable.vue'

export default {
  name: 'Material',
  components: {
    CardContentNode,
    MaterialTable
  },
  mixins: [contentNodeMixin],
  computed: {
    materialItemCollection () {
      return this.api.get().materialItems({ materialNode: this.contentNode._meta.self })
    }
  }
}
</script>

<style scoped>

</style>

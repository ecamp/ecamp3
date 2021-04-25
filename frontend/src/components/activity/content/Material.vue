<template>
  <card-content-node v-bind="$props">
    <div class="mb-3">
      <material-table :camp="camp"
                      :content-node="contentNode"
                      :layout-mode="layoutMode"
                      :material-item-collection="materialItems" />
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
    materialItems () {
      return this.api.get().materialItems({ contentNodeId: this.contentNode.id })
    }
  }
}
</script>

<style scoped>

</style>

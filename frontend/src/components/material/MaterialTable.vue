<!--
Displays several tabs with details on a single camp.
-->

<template>
  <MaterialTableDesktop
    v-if="$vuetify.breakpoint.smAndUp"
    :camp="camp"
    :material-item-collection="materialItemCollection"
    :material-list="materialList"
    :period="period"
    :material-node="materialNode"
    show-activity-material
  />
  <MaterialTableMobile
    v-else
    :camp="camp"
    :material-item-collection="materialItemCollection"
    :material-list="materialList"
    :period="period"
    :material-node="materialNode"
    show-activity-material
  />
</template>

<script>
import MaterialTableDesktop from '@/components/material/MaterialTableDesktop.vue'
import MaterialTableMobile from '@/components/material/MaterialTableMobile.vue'

export default {
  name: 'MaterialDetail',
  components: { MaterialTableMobile, MaterialTableDesktop },
  props: {
    materialItemCollection: { type: Object, required: true },
    camp: { type: Object, required: true },
    period: { type: Object, required: false, default: null },
    materialList: { type: Object, required: false, default: null },
    materialNode: { type: Object, required: false, default: null },
  },
  data() {
    return {
      locked: false,
    }
  },
}
</script>

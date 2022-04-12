<!--
Admin screen of a camp: Displays details & periods of a single camp and allows to edit them.
-->

<template>
  <div style="display: contents">
    <DesktopAdmin v-if="$vuetify.breakpoint.smAndUp" :camp="camp" />
    <MobileAdmin v-else :camp="camp" />
  </div>
</template>

<script>
import DesktopAdmin from './admin/DesktopAdmin.vue'
import MobileAdmin from './admin/MobileAdmin.vue'

export default {
  name: 'Admin',
  components: {
    DesktopAdmin,
    MobileAdmin
  },
  props: {
    camp: { type: Function, required: true }
  }
}
</script>

<style></style>

<!--
Admin screen of a camp: Displays details & periods of a single camp and allows to edit them.
-->

<template>
  <camp-print :camp="camp" />
</template>

<script>
import CampPrint from '@/components/camp/CampPrint.vue'

export default {
  name: 'PrintCamp',
  components: {
    CampPrint
  },
  props: {
    camp: { type: Function, required: true }
  },
  data () {
    return {}
  }
}
</script>

<style scoped>
</style>

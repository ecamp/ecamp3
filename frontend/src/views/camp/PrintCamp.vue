<!--
Print preview to print complete camp
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

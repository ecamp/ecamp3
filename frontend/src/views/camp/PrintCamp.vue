<!--
Print preview to print complete camp
-->

<template>
  <print-configurator :camp="camp" />
</template>

<script>
import PrintConfigurator from '@/components/print/PrintConfigurator.vue'

export default {
  name: 'PrintCamp',
  components: {
    PrintConfigurator
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

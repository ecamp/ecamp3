<!--
Basic layout for print preview
-->

<template>
  <content-card :title="$tc('views.camp.print.title')" toolbar>
    <print-configurator :camp="camp" />
  </content-card>
</template>

<script>
import ContentCard from '@/components/layout/ContentCard.vue'
import PrintConfigurator from '@/components/print/PrintConfigurator.vue'

export default {
  name: 'Print',
  components: {
    ContentCard,
    PrintConfigurator,
  },
  props: {
    camp: { type: Function, required: true },
  },
  data() {
    return {}
  },
}
</script>

<style scoped></style>

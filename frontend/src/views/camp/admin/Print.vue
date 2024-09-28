<!--
Basic layout for print preview
-->

<template>
  <content-card :title="$tc('views.admin.print.title')" toolbar>
    <template #title-actions>
      <v-btn text class="mr-n2" @click="$refs.printConfigurator?.resetConfig()">
        <v-icon v-if="$vuetify.breakpoint.smAndUp" left>mdi-file-restore-outline</v-icon>
        {{ $tc('global.button.reset') }}
      </v-btn>
    </template>
    <print-configurator ref="printConfigurator" :camp="camp" />
  </content-card>
</template>

<script>
import ContentCard from '@/components/layout/ContentCard.vue'
import PrintConfigurator from '@/components/print/PrintConfigurator.vue'

export default {
  name: 'AdminPrint',
  components: {
    ContentCard,
    PrintConfigurator,
  },
  props: {
    camp: { type: Object, required: true },
  },
  data() {
    return {}
  },
}
</script>

<style scoped></style>

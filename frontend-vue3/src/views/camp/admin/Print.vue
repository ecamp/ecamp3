<!--
Basic layout for print preview
-->

<template>
  <content-card :title="$t('views.camp.admin.print.title')" toolbar>
    <template #title-actions>
      <v-btn text class="mr-n2" @click="$refs.printConfigurator?.resetConfig()">
        <v-icon v-if="$vuetify.display.smAndUp" left>mdi-file-restore-outline</v-icon>
        {{ $t('global.button.reset') }}
      </v-btn>
    </template>
    <print-configurator ref="printConfigurator" :camp="camp" />
  </content-card>
</template>

<script>
import ContentCard from '@/components/layout/ContentCard.vue'
import PrintConfigurator from '@/components/print/PrintConfigurator.vue'

export default {
  name: 'CampAdminPrint',
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
  head() {
    return {
      title: this.$t('views.camp.admin.print.title'),
    }
  },
}
</script>

<style scoped></style>

<!--
Basic layout for print preview
-->

<template>
  <content-card :title="$t('views.camp.admin.print.title')" class="e-print-card" toolbar>
    <template #title-actions>
      <v-btn variant="text" @click="$refs.printConfigurator?.resetConfig()">
        <v-icon v-if="$vuetify.display.smAndUp" start>mdi-file-restore-outline</v-icon>
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

<style scoped>
.e-print-card {
  display: flex;
  flex-direction: column;
  height: calc(100dvh - var(--v-layout-top) - var(--v-layout-bottom) - 24px);
}

.e-print-card :deep(.ec-content-card__content) {
  flex: 1 1 0;
  min-height: 0;
  height: auto !important;
}

@media (max-width: 959px) {
  .e-print-card {
    height: calc(100dvh - var(--v-layout-top) - var(--v-layout-bottom));
  }
}

:deep(.v-toolbar-title .v-spacer) {
  flex-grow: 0;
}
</style>

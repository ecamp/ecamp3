<!--
Basic layout for print preview
-->

<template>
  <content-card
    v-resizeobserver:0.immediate="onResize"
    :title="$t('views.camp.admin.print.title')"
    class="e-print-card"
    toolbar
  >
    <template #title-actions>
      <v-btn
        variant="text"
        class="me-2"
        :aria-expanded="viewMode !== 'config'"
        aria-controls="print-preview"
        @click="togglePreview"
      >
        {{ previewButtonLabel }}
      </v-btn>
    </template>
    <print-configurator :view-mode="viewMode" :is-wide="isWide" :camp="camp" />
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
    return { viewMode: 'config', containerWidth: 0 }
  },
  head() {
    return {
      title: this.$t('views.camp.admin.print.title'),
    }
  },
  computed: {
    isWide() {
      return this.containerWidth > 1024
    },
    previewButtonLabel() {
      if (this.viewMode === 'config') {
        return this.$t('components.print.printConfigurator.preview')
      }
      return this.isWide
        ? this.$t('components.print.printConfigurator.collapsePreview')
        : this.$t('global.button.back')
    },
  },
  watch: {
    isWide(isWide) {
      this.viewMode = isWide ? 'split' : 'config'
    },
  },
  methods: {
    onResize({ width }) {
      this.containerWidth = width
    },
    togglePreview() {
      this.viewMode = this.viewMode === 'config' ? 'preview' : 'config'
    },
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

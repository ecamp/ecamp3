<template>
  <div class="button-wrapper">
    <v-btn
      color="primary"
      prepend-icon="mdi-download"
      :text="$t('components.print.generator.downloadPdfButton.label')"
      :disabled="loading"
      @click="generatePdf"
    />
    <div class="progress-wrapper">
      <v-progress-circular
        v-if="loading"
        :model-value="progress"
        size="24"
      ></v-progress-circular>
      <span v-if="loading">{{ state }}</span>
    </div>
  </div>
</template>

<script>
import { generatePdfMixin } from './generatePdfMixin.js'
import { useToast } from '@/components/toast/useToast.js'

export default {
  name: 'DownloadPdfButton',
  mixins: [generatePdfMixin],
  setup() {
    const toast = useToast()
    return { toast }
  },
}
</script>
<style scoped lang="scss">
@use 'vuetify/settings';
@use 'sass:map';

.button-wrapper {
  display: flex;
  flex-direction: row;
  gap: 8px;
  flex-wrap: wrap;
  align-items: center;
}
.progress-wrapper {
  display: flex;
  flex-direction: row;
  gap: 8px;
  align-items: center;
}
</style>

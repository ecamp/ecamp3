<template>
  <div class="button-wrapper">
    <v-btn color="primary" variant="outlined" :disabled="loading" @click="generatePdf">
      <v-icon>mdi-printer</v-icon>
      <div class="mx-1">
        {{ $t('components.print.printClient.downloadClientPdfButton.label') }}
      </div>
    </v-btn>
    <div class="progress-wrapper">
      <v-progress-circular
        v-if="loading"
        :model-value="progress"
        size="24"
        rotate="270"
      ></v-progress-circular>
      <span v-if="loading">{{ state }}</span>
    </div>
  </div>
</template>

<script>
import { generatePdfMixin } from './generatePdfMixin.js'
import { useToast } from 'vue-toastification'

export default {
  name: 'DownloadClientPdfButton',
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
  flex-direction: column;
  gap: 8px;
  align-items: flex-start;
  @media #{map.get(settings.$display-breakpoints, 'lg-and-up')} {
    flex-direction: row;
    align-items: center;
  }
}
.progress-wrapper {
  display: flex;
  flex-direction: row;
  gap: 8px;
  align-items: center;
  @media #{map.get(settings.$display-breakpoints, 'md-and-down')} {
    width: 100%;
    justify-content: flex-start;
  }
}
</style>

<template>
  <div>
    <v-btn :loading="loading" color="primary" variant="outlined" @click="generatePdf">
      <v-icon>mdi-printer</v-icon>
      <div class="mx-1">
        {{ $t('components.print.printNuxt.downloadNuxtPdfButton.label') }}
      </div>
    </v-btn>
  </div>
</template>

<script>
import { generatePdfMixin } from './generatePdfMixin.js'
import { useToast } from '@/components/toast/useToast.js'

export default {
  name: 'DownloadNuxtPdfButton',
  mixins: [generatePdfMixin],
  setup() {
    const toast = useToast()
    return { toast }
  },
}
</script>

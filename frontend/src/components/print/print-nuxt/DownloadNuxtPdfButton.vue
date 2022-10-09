<template>
  <div>
    <v-btn color="primary" :loading="loading" outlined @click="generatePdf">
      <v-icon>mdi-printer</v-icon>
      <div class="mx-1">
        {{ $tc('components.print.printNuxt.downloadNuxtPdfListItem.label') }}
      </div>
    </v-btn>
  </div>
</template>

<script>
import { generatePdfMixin } from './generatePdfMixin.js'

export default {
  name: 'DownloadNuxtPdfButton',
  mixins: [generatePdfMixin],
}
</script>

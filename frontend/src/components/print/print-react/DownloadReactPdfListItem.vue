<template>
  <v-list-item :disabled="loading" @click.stop="generatePdf">
    <v-list-item-icon>
      <v-icon v-if="loading" class="mdi-spin">mdi-loading</v-icon>
      <v-icon v-else>mdi-printer</v-icon>
    </v-list-item-icon>
    <v-list-item-title>
      {{ $tc('components.print.printReact.downloadReactPdfListItem.label') }}
    </v-list-item-title>
  </v-list-item>
</template>

<script>
import { generatePdfMixin } from './generatePdfMixin.js'

export default {
  name: 'DownloadReactPdfListItem',
  mixins: [generatePdfMixin],
}
</script>

<style scoped></style>

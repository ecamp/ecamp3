<template>
  <div>
    <v-btn color="primary" :loading="loading" outlined @click="generatePdf">
      <v-icon>mdi-printer</v-icon>
      <div class="mx-1">
        {{ $tc('components.print.printNuxt.downloadNuxtPdfListItem.label') }}
      </div>
    </v-btn>
    <v-snackbar v-model="error" :timeout="10000">
      {{ $tc('components.print.localPdfDownloadButton.error') }}
      <template #action="{ attrs }">
        <v-btn color="red" text v-bind="attrs" @click="error = null">
          {{ $tc('global.button.close') }}
        </v-btn>
      </template>
    </v-snackbar>
  </div>
</template>

<script>
import { generatePdfMixin } from './generatePdfMixin.js'

export default {
  name: 'DownloadNuxtPdfButton',
  mixins: [generatePdfMixin],
}
</script>

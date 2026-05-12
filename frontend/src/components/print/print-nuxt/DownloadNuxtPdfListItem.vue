<template>
  <v-list-item
    :disabled="loading"
    :title="$t('components.print.printNuxt.downloadNuxtPdfListItem.label')"
    @click.stop="generatePdf"
  >
    <template #prepend>
      <v-icon v-if="loading" class="mdi-spin" icon="mdi-loading" />
      <v-icon v-else icon="mdi-printer" />
    </template>
  </v-list-item>
</template>

<script>
import { generatePdfMixin } from './generatePdfMixin.js'
import { useToast } from 'vue-toastification'

export default {
  name: 'DownloadNuxtPdfListItem',
  mixins: [generatePdfMixin],
  setup() {
    const toast = useToast()
    return { toast }
  },
}
</script>

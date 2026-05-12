<template>
  <v-list-item
    :readonly="loading"
    :lines="loading ? 'two' : 'one'"
    :title="$t('components.print.printClient.downloadClientPdfListItem.label')"
    :subtitle="loading ? state : undefined"
    @click.stop="generatePdf"
  >
    <template #prepend>
      <v-icon v-if="loading">
        <v-progress-circular
          :model-value="progress"
          :rotate="0"
          size="24"
          color="primary"
        />
      </v-icon>
      <v-icon v-else icon="mdi-printer" />
    </template>
  </v-list-item>
</template>

<script>
import { generatePdfMixin } from './generatePdfMixin.js'
import { useToast } from 'vue-toastification'

export default {
  name: 'DownloadClientPdfListItem',
  mixins: [generatePdfMixin],
  setup() {
    const toast = useToast()
    return { toast }
  },
}
</script>

<style scoped></style>

<template>
  <div class="px-md-4">
    <v-file-input
      :model-value="selectedFile"
      :label="$t('components.print.config.insertPdfConfig.file')"
      accept="application/pdf"
      :hint="pageCountHint"
      :error-messages="fileErrors"
      persistent-hint
      variant="underlined"
      prepend-icon="mdi-file-pdf-box"
      @update:model-value="selectFile"
    />
    <e-text-field
      v-model="options.pageRange"
      path="pageRange"
      :label="$t('components.print.config.insertPdfConfig.pageRange')"
      :hint="$t('components.print.config.insertPdfConfig.pageRangeHint')"
      persistent-hint
      variant="underlined"
      @update:model-value="$emit('update:modelValue', modelValue)"
    />
    <e-text-field
      v-model="options.tocTitle"
      path="tocTitle"
      :label="$t('components.print.config.insertPdfConfig.tocTitle')"
      variant="underlined"
      @update:model-value="$emit('update:modelValue', modelValue)"
    />
    <e-checkbox
      v-model="options.fitToPageSize"
      path="fitToPageSize"
      input-class="ml-n2"
      density="comfortable"
      :label="$t('components.print.config.insertPdfConfig.fitToPageSize')"
      @update:model-value="$emit('update:modelValue', modelValue)"
    />
  </div>
</template>

<script>
import { deleteAsset, saveAsset } from '../generator/assetStore.js'

export default {
  name: 'InsertPdfConfig',
  props: {
    modelValue: { type: Object, required: true },
  },
  emits: ['update:modelValue'],
  data() {
    return {
      fileErrors: [],
    }
  },
  computed: {
    options: {
      get() {
        return this.modelValue
      },
      set(v) {
        this.$emit('update:modelValue', v)
      },
    },
    selectedFile() {
      return this.options.file ? new File([], this.options.file.name) : null
    },
    pageCountHint() {
      if (!this.options.file) return ''
      const count = this.options.file.pages.length
      return this.$t(
        'components.print.config.insertPdfConfig.pageCount',
        { count },
        count
      )
    },
  },
  methods: {
    async selectFile(files) {
      const file = Array.isArray(files) ? files[0] : files
      this.fileErrors = []
      const previousAssetId = this.options.file?.assetId
      try {
        this.options.file = file ? await storePdf(file) : null
      } catch {
        this.fileErrors = [this.$t('components.print.config.insertPdfConfig.fileError')]
        return
      }
      if (previousAssetId) await deleteAsset(previousAssetId)
      this.$emit('update:modelValue', this.options)
    },
  },
  defaultOptions() {
    return {
      file: null,
      tocTitle: '',
      pageRange: '',
      fitToPageSize: true,
    }
  },
  design: {
    multiple: true,
  },
  repairConfig(config) {
    if (!config.options) config.options = {}
    const file = config.options.file
    if (!file?.assetId || !Array.isArray(file.pages)) config.options.file = null
    if (typeof config.options.tocTitle !== 'string') config.options.tocTitle = ''
    if (typeof config.options.pageRange !== 'string') config.options.pageRange = ''
    if (typeof config.options.fitToPageSize !== 'boolean') {
      config.options.fitToPageSize = true
    }
    return config
  },
}

async function storePdf(file) {
  const { PDFDocument } = await import('pdf-lib')
  const pdf = await PDFDocument.load(await file.arrayBuffer())
  const pages = pdf.getPages().map((page) => page.getSize())
  const assetId = await saveAsset(file)
  return { assetId, name: file.name, pages }
}
</script>

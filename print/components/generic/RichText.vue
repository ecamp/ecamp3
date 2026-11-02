<template>
  <!-- eslint-disable-next-line vue/no-v-html -->
  <div class="tw-prose tw-prose-neutral tw-break-anywhere" v-html="purifiedHtml" />
</template>

<script>
import { prepareRichText } from '@/helpers/prepareRichText.js'

export default {
  props: {
    richText: { type: String, default: '' },
  },
  async setup(props) {
    const config = useRuntimeConfig()
    const headers = useRequestHeaders(['cookie'])
    if (config.basicAuthToken) headers.Authorization = `Basic ${config.basicAuthToken}`
    const imageHtml = await prepareRichText(props.richText, async (path) => {
      const data = await $fetch(
        `${config.internalApiRootUrl.replace(/\/$/, '')}/${path}`,
        {
          headers: { ...headers, Accept: 'image/webp' },
          responseType: 'arrayBuffer',
        }
      )
      return `data:image/webp;base64,${Buffer.from(data).toString('base64')}`
    })
    return { imageHtml }
  },
  computed: {
    purifiedHtml() {
      return this.imageHtml
    },
  },
}
</script>

<style scoped>
div :deep(img) {
  max-width: 100%;
  max-height: 140mm;
  height: auto;
  object-fit: contain;
  break-inside: avoid;
}
</style>

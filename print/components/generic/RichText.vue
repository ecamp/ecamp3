<template>
  <!-- eslint-disable-next-line vue/no-v-html -->
  <div class="tw-prose tw-prose-neutral" v-html="purifiedHtml" />
</template>

<script>
import DOMPurify from 'isomorphic-dompurify'

export default {
  props: {
    richText: { type: String, default: '' },
  },
  computed: {
    purifiedHtml() {
      return DOMPurify.sanitize(this.richText)

      // we could also be more restrictive and whitelist the allowed tags
      // at the moment not needed, as we already whitelist tags on the server
      // this is really more of a safety-net, if anything goes wrong on the server-side
      //
      // return DOMPurify.sanitize(this.richText, { ALLOWED_TAGS: ['p', 'strong'] })
    },
  },
}
</script>

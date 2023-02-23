<template>
  <pdf-text v-bind="$props">
    <slot />
  </pdf-text>
</template>
<script>
export default {
  name: 'Text',
  props: {
    title: { type: String, default: undefined },
    author: { type: String, default: undefined },
    subject: { type: String, default: undefined },
    keywords: { type: String, default: undefined },
    creator: { type: String, default: undefined },
    producer: { type: String, default: undefined },
    pdfVersion: { type: String, default: undefined },
    language: { type: String, default: undefined },
    pageMode: { type: String, default: undefined },
    pageLayout: { type: String, default: undefined },
  },
}
</script>

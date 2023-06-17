<template>
  <pdf-document v-bind="$props" :on-render="$attrs.onRender">
    <slot />
  </pdf-document>
</template>
<script>
// https://react-pdf.org/components#document
export default {
  name: 'Document',
  props: {
    title: { type: String, default: undefined },
    author: { type: String, default: undefined },
    subject: { type: String, default: undefined },
    keywords: { type: String, default: undefined },
    creator: { type: String, default: undefined },
    producer: { type: String, default: '' },
    pdfVersion: { type: String, default: '1.3' },
    language: { type: String, default: undefined },
    pageMode: { type: String, default: 'useNone' },
    pageLayout: { type: String, default: 'singlePage' },
    onRender: { type: Function, default: undefined },
  },
}
</script>

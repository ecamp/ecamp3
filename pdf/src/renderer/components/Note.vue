<template>
  <pdf-note v-bind="$props">
    <slot />
  </pdf-note>
</template>
<script>
// https://react-pdf.org/components#note
export default {
  name: 'Note',
  props: {
    // "style" is not an allowed prop name in Vue, even though we don't render to HTML
    //style: { type: [Object, Array], default: undefined },
    children: { type: String, default: undefined },
    fixed: { type: Boolean, default: false },
  },
}
</script>

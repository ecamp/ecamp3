<template>
  <pdf-page v-bind="$props">
    <slot />
  </pdf-page>
</template>
<script>
// https://react-pdf.org/components#page
export default {
  name: 'Page',
  props: {
    size: { type: String, default: 'A4' },
    orientation: { type: String, default: 'portrait' },
    wrap: { type: Boolean, default: true },
    styles: { type: [Object, Array], default: undefined },
    debug: { type: Boolean, default: false },
    dpi: { type: Number, default: 72 },
    bookmark: { type: String, default: undefined },
  },
}
</script>

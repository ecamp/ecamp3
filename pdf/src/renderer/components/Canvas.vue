<template>
  <pdf-canvas v-bind="$props">
    <slot />
  </pdf-canvas>
</template>
<script>
// https://react-pdf.org/components#canvas
export default {
  name: 'Canvas',
  props: {
    // "style" is not an allowed prop name in Vue, even though we don't render to HTML
    //style: { type: [Object, Array], default: undefined },
    paint: { type: Function, default: undefined },
    debug: { type: Boolean, default: false },
    fixed: { type: Boolean, default: false },
    bookmark: { type: String, default: undefined },
  },
}
</script>

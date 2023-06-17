<template>
  <pdf-svg v-bind="$props">
    <slot />
  </pdf-svg>
</template>
<script>
// https://react-pdf.org/svg#svg
export default {
  name: 'Svg',
  props: {
    width: { type: [String, Number], default: undefined },
    height: { type: [String, Number], default: undefined },
    viewBox: { type: String, default: undefined },
    preserveAspectRatio: { type: String, default: undefined },
    // "style" is not an allowed prop name in Vue, even though we don't render to HTML
    //style: { type: [Object, Array], default: undefined },
  },
}
</script>

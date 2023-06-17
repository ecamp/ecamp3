<template>
  <pdf-image v-bind="$props">
    <slot />
  </pdf-image>
</template>
<script>
// https://react-pdf.org/components#image
export default {
  name: 'Image',
  props: {
    src: { type: [String, Object, Function], default: undefined },
    source: { type: [String, Object, Function], default: undefined },
    // "style" is not an allowed prop name in Vue, even though we don't render to HTML
    //style: { type: [Object, Array], default: undefined },
    debug: { type: Boolean, default: false },
    fixed: { type: Boolean, default: false },
    cache: { type: Boolean, default: true },
    bookmark: { type: String, default: undefined },
  },
}
</script>

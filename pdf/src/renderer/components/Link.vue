<template>
  <pdf-link v-bind="$props">
    <slot />
  </pdf-link>
</template>
<script>
// https://react-pdf.org/components#link
export default {
  name: 'Link',
  props: {
    src: { type: String, default: undefined },
    wrap: { type: Boolean, default: true },
    // "style" is not an allowed prop name in Vue, even though we don't render to HTML
    //style: { type: [Object, Array], default: undefined },
    debug: { type: Boolean, default: false },
    fixed: { type: Boolean, default: false },
    bookmark: { type: [String, Object], default: undefined },
  },
}
</script>

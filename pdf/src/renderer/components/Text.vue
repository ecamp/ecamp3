<template>
  <pdf-text v-bind="$props">
    <slot />
  </pdf-text>
</template>
<script>
export default {
  name: 'Text',
  props: {
    wrap: { type: Boolean, default: true },
    render: { type: Function, default: undefined },
    // "style" is not an allowed prop name in Vue, even though we don't render to HTML
    //style: { type: [Object, Array], default: undefined },
    debug: { type: Boolean, default: false },
    fixed: { type: Boolean, default: false },
    hyphenationCallback: { type: Function, default: undefined },
    id: { type: String, default: undefined },
    bookmark: { type: String, default: undefined },
  },
}
</script>

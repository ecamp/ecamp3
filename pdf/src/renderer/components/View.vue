<template>
  <pdf-view v-bind="$props">
    <slot />
  </pdf-view>
</template>
<script>
export default {
  name: 'View',
  props: {
    wrap: { type: Boolean, default: true },
    // "style" is not an allowed prop name in Vue, even though we don't render to HTML
    //style: { type: [Object, Array], default: undefined },
    render: { type: Function, default: undefined },
    debug: { type: Boolean, default: false },
    fixed: { type: Boolean, default: false },
    id: { type: String, default: undefined },
    bookmark: { type: String, default: undefined },
  },
}
</script>

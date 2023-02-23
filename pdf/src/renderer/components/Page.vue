<template>
  <pdf-page v-bind="$props">
    <slot />
  </pdf-page>
</template>
<script>
export default {
  name: 'Page',
  props: {
    size: { type: String, default: undefined },
    orientation: { type: String, default: undefined },
    wrap: { type: String, default: undefined },
    style: { type: String, default: undefined },
    debug: { type: String, default: undefined },
    dpi: { type: String, default: undefined },
    bookmark: { type: String, default: undefined },
  },
}
</script>

<template>
  <pdf-linear-gradient v-bind="$props">
    <slot />
  </pdf-linear-gradient>
</template>
<script>
// https://react-pdf.org/svg#linear-gradient
export default {
  name: 'LinearGradient',
  props: {
    x1: { type: [String, Number], default: undefined },
    x2: { type: [String, Number], default: undefined },
    y1: { type: [String, Number], default: undefined },
    y2: { type: [String, Number], default: undefined },
  },
}
</script>

<template>
  <pdf-radial-gradient v-bind="$props">
    <slot />
  </pdf-radial-gradient>
</template>
<script>
// https://react-pdf.org/svg#radial-gradient
export default {
  name: 'RadialGradient',
  props: {
    cx: { type: [String, Number], default: undefined },
    cy: { type: [String, Number], default: undefined },
    fr: { type: [String, Number], default: undefined },
    fx: { type: [String, Number], default: undefined },
    fy: { type: [String, Number], default: undefined },
  },
}
</script>

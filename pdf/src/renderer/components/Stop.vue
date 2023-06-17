<template>
  <pdf-stop v-bind="$props">
    <slot />
  </pdf-stop>
</template>
<script>
// https://react-pdf.org/svg#stop
export default {
  name: 'Stop',
  props: {
    offset: { type: [String, Number], default: undefined },
    stopColor: { type: String, default: undefined },
    stopOpacity: { type: [String, Number], default: 1 },
  },
}
</script>

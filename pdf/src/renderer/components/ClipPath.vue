<template>
  <pdf-clip-path v-bind="$props">
    <slot />
  </pdf-clip-path>
</template>
<script>
// https://react-pdf.org/svg#clip-path
export default {
  name: 'ClipPath',
}
</script>

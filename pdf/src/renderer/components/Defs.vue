<template>
  <pdf-defs v-bind="$props">
    <slot />
  </pdf-defs>
</template>
<script>
// https://react-pdf.org/svg#defs
export default {
  name: 'Defs',
}
</script>

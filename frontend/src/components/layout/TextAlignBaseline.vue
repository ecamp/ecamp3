<template>
  <span class="e-bc"><slot /></span>
</template>

<script>
export default {
  name: 'TextAlignBaseline',
  props: {
    smaller: Boolean,
  },
}
</script>

<style scoped lang="scss">
.e-bc {
  display: inline-flex;
  vertical-align: baseline;
  align-items: center;

  &::before {
    user-select: none;
    pointer-events: none;
    content: '\200B';
    font-size: 1em;
    line-height: 1;
  }
}
</style>

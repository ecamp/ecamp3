<template>
  <span :class="['e-bc', smaller && 'e-bc--smaller']"
    ><span class="content"><slot /></span
  ></span>
</template>

<script>
export default {
  name: 'BaselineCenter',
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

  &.e-bc--smaller .content {
    font-size: 0.75em;
  }
}
</style>

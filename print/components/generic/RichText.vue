<template>
  <!-- eslint-disable-next-line vue/no-v-html -->
  <div v-html="richText" />
</template>

<script>
export default {
  props: {
    richText: { type: String, default: '' },
  },
}
</script>

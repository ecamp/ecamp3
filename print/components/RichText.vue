<template>
  <div v-html="richText" />
</template>

<script>
export default {
  props: {
    richText: { type: String, default: '' },
  },
}
</script>

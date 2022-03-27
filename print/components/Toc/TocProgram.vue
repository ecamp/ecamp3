<template>
  <a :href="`#content_${index}_cover`">{{ $tc('print.cover.program') }}</a>
</template>

<script>
export default {
  name: 'TocCover',
  props: {
    options: { type: Object, required: false, default: null },
    camp: { type: Object, required: true },
    index: { type: Number, required: true },
  },
  data() {
    return {}
  },
  async fetch() {},
}
</script>

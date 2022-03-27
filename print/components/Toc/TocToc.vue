<template>
  <a :href="`#content_${index}_toc`">{{ $tc('print.toc.title') }}</a>
</template>

<script>
export default {
  name: 'TocToc',
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

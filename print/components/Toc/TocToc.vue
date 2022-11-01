<template>
  <li>
    <div class="toc-element toc-element-level-1">
      <a :href="`#content_${index}_toc`">{{ $tc('components.toc.tocToc.title') }}</a>
    </div>
  </li>
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

<template>
  <div class="tw-break-after-page">
    <h1 :id="`content_${index}_toc`" class="tw-text-2xl tw-font-bold">
      {{ $tc('components.config.toc.title') }}
    </h1>
    <ul class="toc">
      <template v-for="(content, idx) in config.contents">
        <component
          :is="'Toc' + content.type"
          :key="idx"
          :options="content.options"
          :camp="camp"
          :config="config"
          :index="idx"
        />
      </template>
    </ul>
  </div>
</template>

<script>
export default {
  name: 'ConfigToc',
  props: {
    options: { type: Object, required: false, default: null },
    camp: { type: Object, required: true },
    config: { type: Object, required: true },
    index: { type: Number, required: true },
  },
  data() {
    return {}
  },
  async fetch() {},
}
</script>

<style scoped>
ul.toc {
  list-style: none;
}

/* needs implementation of https://bugs.chromium.org/p/chromium/issues/detail?id=740497
ul.toc .toc-element a::after {
  content: leader('.') " p. " target-counter(attr(href url), page);
  float: right;
}
*/

ul.toc:deep(.toc-element-level-1) {
  margin-top: 25px;
  font-weight: bold;
}

ul.toc:deep(.toc-element-level-2) {
  margin-left: 25px;
}
</style>

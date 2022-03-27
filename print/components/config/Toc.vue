<template>
  <div class="tw-break-after-page">
    <h1 :id="`content_${index}_toc`" class="tw-text-2xl tw-font-bold">
      {{ $tc('print.toc.title') }}
    </h1>
    <ul class="toc">
      <li
        v-for="(content, idx) in config.contents"
        :key="idx"
        class="toc-element toc-element-level-1"
      >
        <component
          :is="'Toc' + content.type"
          :options="content.options"
          :camp="camp"
          :config="config"
          :index="idx"
        />
      </li>
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

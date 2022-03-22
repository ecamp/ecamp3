<template>
  <div class="page_break">
    <h1>Cover page for camp {{ camp.name }}</h1>
    {{ JSON.stringify(options) }}
  </div>
</template>

<script>
export default {
  name: 'ConfigCover',
  props: {
    options: { type: Object, required: false, default: null },
    camp: { type: Object, required: true },
  },
  data() {
    return {}
  },
  async fetch() {},
}
</script>

<style lang="scss" scoped>
@media print {
  .page_break {
    page-break-after: always;
  }
}
</style>

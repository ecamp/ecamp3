<template>
  <div class="tw-break-after-page">
    <h1>Table of content {{ camp.name }}</h1>
    {{ JSON.stringify(options) }}
  </div>
</template>

<script>
export default {
  name: 'ConfigToc',
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

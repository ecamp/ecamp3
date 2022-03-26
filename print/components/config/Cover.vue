<template>
  <div class="tw-break-after-page tw-text-center">
    <div class="tw-text-4xl tw-mt-20 tw-mb-20">{{ camp.name }}</div>

    <div class="tw-mb-6">
      <span class="tw-font-bold">{{ $tc('entity.camp.fields.title') }}:</span>
      {{ camp.title }}
    </div>
    <div>
      <span class="tw-font-bold">{{ $tc('entity.camp.fields.motto') }}:</span>
      {{ camp.motto }}
    </div>
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

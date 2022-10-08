<template>
  <div class="tw-break-after-page tw-text-center">
    <div :id="`content_${index}_cover`" class="tw-text-6xl tw-mt-20 tw-mb-40">
      {{ camp.name }}
    </div>

    <div class="tw-mb-12 tw-text-3xl">
      <span class="tw-font-bold">{{ $tc('entity.camp.fields.title') }}:</span>
      {{ camp.title }}
    </div>
    <div class="tw-text-3xl">
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
    config: { type: Object, required: true },
    index: { type: Number, required: true },
  },
  data() {
    return {}
  },
  async fetch() {},
}
</script>

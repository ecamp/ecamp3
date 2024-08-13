<template>
  <div class="tw-break-after-page tw-text-center">
    <div class="tw-font-medium tw-mt-20 tw-text-xl">
      {{ camp.name }}
    </div>

    <div :id="`content_${index}_cover`" class="tw-my-12 tw-text-6xl tw-font-semibold">
      {{ camp.title }}
    </div>
    <div class="tw-text-3xl">
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
}
</script>

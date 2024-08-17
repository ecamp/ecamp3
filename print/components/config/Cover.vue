<template>
  <div class="tw-break-after-page tw-text-center">
    <p class="tw-font-medium tw-mt-20 tw-text-3xl">{{ camp.organizer }}</p>

    <h1 :id="`content_${index}_cover`" class="tw-my-16 tw-text-6xl tw-font-semibold">
      {{ camp.title }}
    </h1>

    <p class="tw-text-5xl tw-font-medium">
      {{ camp.motto }}
    </p>
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

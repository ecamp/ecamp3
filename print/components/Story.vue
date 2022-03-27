<template>
  <div class="tw-break-after-page">
    <h1
      :id="`content_${index}_period_${period.id}`"
      class="tw-text-2xl tw-font-bold"
    >
      {{ $tc('print.story.title') }}: {{ $tc('entity.period.name') }}
      {{ period.description }}
    </h1>
  </div>
</template>

<script>
export default {
  props: {
    camp: { type: Object, required: true },
    period: {
      type: Object,
      required: true,
    },
    index: { type: Number, required: true },
  },
}
</script>

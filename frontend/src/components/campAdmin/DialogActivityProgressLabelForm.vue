<template>
  <e-form name="activityProgressLabel">
    <e-text-field
      v-model="localActivityProgressLabel.title"
      path="title"
      autofocus
      vee-rules="required"
    />
  </e-form>
</template>

<script>
export default {
  name: 'DialogActivityProgressLabelForm',
  props: {
    activityProgressLabel: { type: Object, required: true },
  },
  computed: {
    localActivityProgressLabel() {
      return this.activityProgressLabel
    },
  },
}
</script>

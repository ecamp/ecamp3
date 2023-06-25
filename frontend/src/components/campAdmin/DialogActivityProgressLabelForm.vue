<template>
  <div>
    <e-text-field
      v-model="localActivityProgressLabel.title"
      :name="$tc('entity.activityProgressLabel.fields.title')"
      autofocus
      vee-rules="required"
    />
  </div>
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

<template>
  <e-form name="activityProgressLabel">
    <e-text-field
      v-model="localActivityProgressLabel.title"
      name="title"
      autofocus
      vee-rules="required"
    />
  </e-form>
</template>

<script>
import EForm from '@/components/form/base/EForm.vue'

export default {
  name: 'DialogActivityProgressLabelForm',
  components: { EForm },
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

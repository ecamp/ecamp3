<template>
  <dialog-form
    v-model="showDialog"
    :loading="loading"
    :error="error"
    icon="mdi-package-variant"
    :title="progressLabel.title"
    :submit-action="update"
    submit-color="success"
    :cancel-action="close"
  >
    <template #activator="scope">
      <slot name="activator" v-bind="scope" />
    </template>
    <dialog-activity-progress-label-form :activity-progress-label="entityData" />
  </dialog-form>
</template>

<script>
import DialogBase from '@/components/dialog/DialogBase.vue'
import DialogForm from '@/components/dialog/DialogForm.vue'
import DialogActivityProgressLabelForm from './DialogActivityProgressLabelForm.vue'

export default {
  name: 'DialogActivityProgressLabelEdit',
  components: { DialogForm, DialogActivityProgressLabelForm },
  extends: DialogBase,
  props: {
    progressLabel: { type: Object, required: true },
  },
  data() {
    return {
      entityProperties: ['title'],
    }
  },
  watch: {
    // copy data whenever dialog is opened
    showDialog: function (showDialog) {
      if (showDialog) {
        this.loadEntityData(this.progressLabel._meta.self)
      }
    },
  },
}
</script>

<style scoped></style>

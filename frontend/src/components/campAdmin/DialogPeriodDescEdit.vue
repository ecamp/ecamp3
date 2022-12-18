<template>
  <dialog-form
    v-model="showDialog"
    :loading="loading"
    :error="error"
    icon="mdi-package-variant"
    :title="period.description"
    :submit-action="update"
    submit-color="success"
    :cancel-action="close"
  >
    <template #activator="scope">
      <slot name="activator" v-bind="scope" />
    </template>
    <div>
      <e-text-field
        v-model="entityData.description"
        :name="$tc('entity.period.fields.description')"
        autofocus
        vee-rules="required"
      />
    </div>
  </dialog-form>
</template>

<script>
import DialogBase from '@/components/dialog/DialogBase.vue'
import DialogForm from '@/components/dialog/DialogForm.vue'

export default {
  name: 'DialogPeriodDescEdit',
  components: { DialogForm },
  extends: DialogBase,
  props: {
    period: { type: Object, required: true },
  },
  data() {
    return {
      entityProperties: ['description'],
    }
  },
  watch: {
    // copy data whenever dialog is opened
    showDialog: function (showDialog) {
      if (showDialog) {
        this.loadEntityData(this.period._meta.self)
      }
    },
  },
}
</script>

<style scoped></style>

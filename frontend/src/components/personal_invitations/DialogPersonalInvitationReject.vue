<template>
  <DialogForm
    v-model="showDialog"
    type="error"
    icon="mdi-email"
    :title="
      $tc(
        'components.personalInvitations.dialogPersonalInvitationReject.rejectInvitation'
      )
    "
    :error="error"
    :submit-action="submitAction"
    :submit-label="
      $tc(
        'components.personalInvitations.dialogPersonalInvitationReject.rejectInvitation'
      )
    "
    submit-color="error"
    submit-icon="mdi-cancel"
    cancel-icon=""
    :cancel-action="close"
    v-bind="$attrs"
  >
    <template #activator="scope">
      <slot name="activator" v-bind="scope" />
    </template>
    <slot>
      {{
        $tc(
          'components.personalInvitations.dialogPersonalInvitationReject.warningText',
          0,
          { campTitle: campTitle }
        )
      }}
    </slot>
    <template v-if="$slots.error || error" #error>
      <slot name="error">
        {{ error }}
      </slot>
    </template>
  </DialogForm>
</template>

<script>
import DialogBase from '@/components/dialog/DialogBase.vue'
import DialogForm from '@/components/dialog/DialogForm.vue'

export default {
  name: 'DialogPersonalInvitationReject',
  components: { DialogForm },
  extends: DialogBase,
  props: {
    entity: { type: Object, required: true },
    campTitle: { type: String, required: true },
  },
  created() {
    this.entityUri = this.entity._meta.self
  },
  methods: {
    submitAction() {
      this.$emit('submit')
    },
  },
}
</script>

<style scoped></style>

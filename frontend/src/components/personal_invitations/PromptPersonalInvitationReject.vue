<template>
  <PopoverPrompt
    v-model="showDialog"
    type="error"
    :error="error"
    :submit-action="submitAction"
    :submit-label="
      $tc(
        'components.personalInvitations.promptPersonalInvitationReject.rejectInvitation'
      )
    "
    submit-color="error"
    submit-icon="mdi-cancel"
    cancel-icon=""
    :cancel-action="close"
    position="top"
    :align="align"
    v-bind="$attrs"
  >
    <template #activator="scope">
      <slot name="activator" v-bind="scope" />
    </template>
    <slot>
      {{
        $tc(
          'components.personalInvitations.promptPersonalInvitationReject.warningText',
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
  </PopoverPrompt>
</template>

<script>
import DialogBase from '@/components/dialog/DialogBase.vue'
import PopoverPrompt from '@/components/prompt/PopoverPrompt.vue'

export default {
  name: 'PromptPersonalInvitationReject',
  components: { PopoverPrompt },
  extends: DialogBase,
  props: {
    entity: { type: Object, required: true },
    campTitle: { type: String, required: true },
    align: { type: String, required: true },
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

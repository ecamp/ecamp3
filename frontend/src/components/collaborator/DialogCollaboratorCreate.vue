<template>
  <dialog-form
    v-model="showDialog"
    :loading="loading"
    :error="error"
    icon="mdi-account-plus"
    :title="$tc('components.collaborator.dialogCollaboratorCreate.title')"
    :submit-action="createCollaboration"
    :submit-label="$tc('components.collaborator.dialogCollaboratorCreate.invite')"
    submit-icon="mdi-email-fast"
    submit-color="success"
    :cancel-action="close"
  >
    <template #activator="scope">
      <slot name="activator" v-bind="scope" />
    </template>

    <e-text-field
      v-model="entityData.inviteEmail"
      type="email"
      :name="$tc('entity.campCollaboration.fields.inviteEmail')"
      vee-rules="required|email"
      class="mb-2"
    />

    <dialog-collaborator-form :collaboration="entityData" />
  </dialog-form>
</template>

<script>
import DialogForm from '@/components/dialog/DialogForm.vue'
import DialogBase from '@/components/dialog/DialogBase.vue'
import DialogCollaboratorForm from '@/components/collaborator/DialogCollaboratorForm.vue'

const DEFAULT_INVITE_ROLE = 'member'

export default {
  name: 'DialogCollaboratorCreate',
  components: { DialogCollaboratorForm, DialogForm },
  extends: DialogBase,
  props: {
    camp: { type: Object, required: true },
  },
  data() {
    return {
      entityProperties: ['camp', 'inviteEmail', 'role'],
      entityUri: '/camp_collaborations',
    }
  },
  watch: {
    showDialog: function (showDialog) {
      if (showDialog) {
        this.setEntityData({
          camp: this.camp._meta.self,
          inviteEmail: '',
          role: DEFAULT_INVITE_ROLE,
        })
      } else {
        // clear form on exit
        this.clearEntityData()
      }
    },
  },
  methods: {
    createCollaboration() {
      return this.create().then(() => {
        this.api.reload(this.camp.materialLists())
      })
    },
  },
}
</script>

<style scoped></style>

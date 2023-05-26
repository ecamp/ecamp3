<template>
  <SettingsForm
    v-model="showDialog"
    :loading="loading"
    :error="error"
    icon="mdi-account-plus"
    :title="$tc('components.collaborator.collaboratorCreate.title')"
    :submit-action="createCollaboration"
    :submit-label="$tc('components.collaborator.collaboratorCreate.invite')"
    submit-icon="mdi-email-fast"
    submit-color="success"
    :cancel-action="close"
  >
    <template #activator="{ on }">
      <ButtonAdd color="success" icon="mdi-account-plus" icon-first v-on="on">
        {{ $tc('components.collaborator.collaboratorCreate.inviteCta') }}
      </ButtonAdd>
    </template>

    <e-text-field
      v-model="entityData.inviteEmail"
      type="email"
      :name="$tc('entity.campCollaboration.fields.inviteEmail')"
      vee-rules="required|email"
      class="mb-2"
    />

    <SettingsCollaboratorForm :collaboration="entityData" />
  </SettingsForm>
</template>

<script>
import SettingsForm from '@/components/dialog/DialogForm.vue'
import DialogBase from '@/components/dialog/DialogBase.vue'
import SettingsCollaboratorForm from '@/components/collaborator/SettingsCollaboratorForm.vue'
import ButtonAdd from '@/components/buttons/ButtonAdd.vue'

const DEFAULT_INVITE_ROLE = 'member'

export default {
  name: 'CollaboratorCreate',
  components: { ButtonAdd, SettingsCollaboratorForm, SettingsForm },
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
        this.api.reload(this.camp.campCollaborations())
      })
    },
  },
}
</script>

<style scoped></style>

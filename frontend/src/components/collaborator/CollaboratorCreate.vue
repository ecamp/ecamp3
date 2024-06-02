<template>
  <DetailPane
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
      <ButtonAdd color="secondary" text class="my-n2" icon="mdi-account-plus" v-on="on">
        {{ $tc('components.collaborator.collaboratorCreate.inviteCta') }}
      </ButtonAdd>
    </template>

    <e-text-field
      v-model="entityData.inviteEmail"
      type="email"
      path="inviteEmail"
      vee-rules="required|email"
      class="mb-2"
    />

    <CollaboratorForm :collaboration="entityData" />
  </DetailPane>
</template>

<script>
import ButtonAdd from '@/components/buttons/ButtonAdd.vue'
import DetailPane from '@/components/generic/DetailPane.vue'
import DialogBase from '@/components/dialog/DialogBase.vue'
import CollaboratorForm from '@/components/collaborator/CollaboratorForm.vue'

const DEFAULT_INVITE_ROLE = 'member'

export default {
  name: 'CollaboratorCreate',
  components: { ButtonAdd, DetailPane, CollaboratorForm },
  extends: DialogBase,
  provide() {
    return {
      entityName: 'campCollaboration',
    }
  },
  props: {
    camp: { type: Object, required: true },
  },
  data() {
    return {
      entityProperties: ['abbr', 'camp', 'color', 'inviteEmail', 'role'],
      entityUri: '',
    }
  },
  watch: {
    showDialog: function (showDialog) {
      if (showDialog) {
        this.setEntityData({
          abbr: '',
          camp: this.camp._meta.self,
          color: '',
          inviteEmail: '',
          role: DEFAULT_INVITE_ROLE,
        })
      } else {
        // clear form on exit
        this.clearEntityData()
      }
    },
  },
  mounted() {
    this.api
      .href(this.api.get(), 'campCollaborations')
      .then((uri) => (this.entityUri = uri))
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

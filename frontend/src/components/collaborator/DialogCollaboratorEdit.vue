<template>
  <dialog-form
    v-model="showDialog"
    :loading="loading"
    :error="error"
    icon="mdi-account-edit"
    :title="$tc('components.collaborator.dialogCollaboratorEdit.title')"
    :submit-action="update"
    :submit-label="$tc('global.button.save')"
    submit-color="success"
    :cancel-action="close"
  >
    <template #moreActions>
      <v-tooltip :disabled="disabled || !isLastManager" top>
        <template #activator="{ on, attrs }">
          <div v-bind="attrs" v-on="on">
            <CollaboratorListItemDeactivate :entity="collaborator">
              <template #activator="{ on: onDialog }">
                <icon-button
                  text
                  icon-first
                  :disabled="(disabled && !isOwnCampCollaboration) || isLastManager"
                  :icon-only="false"
                  icon="mdi-cancel"
                  v-on="onDialog"
                >
                  {{ $tc('components.collaborator.collaboratorListItem.deactivate') }}
                </icon-button>
              </template>
            </CollaboratorListItemDeactivate>
          </div>
        </template>
        <span>{{
            $tc('components.collaborator.collaboratorListItem.cannotRemoveLastManager')
          }}</span>
      </v-tooltip>
    </template>

    <template #activator="scope">
      <slot name="activator" v-bind="scope" />
    </template>

    <dialog-collaborator-form :collaboration="entityData" />
  </dialog-form>
</template>

<script>
import DialogForm from '@/components/dialog/DialogForm.vue'
import DialogBase from '@/components/dialog/DialogBase.vue'
import DialogCollaboratorForm from '@/components/collaborator/DialogCollaboratorForm.vue'
import { campRoleMixin } from '@/mixins/campRoleMixin.js'
import IconButton from '@/components/buttons/IconButton.vue'

const DEFAULT_INVITE_ROLE = 'member'

export default {
  name: 'DialogCollaboratorEdit',
  components: { IconButton, DialogCollaboratorForm, DialogForm },
  extends: DialogBase,
  mixins: [campRoleMixin],
  props: {
    collaborator: { type: Object, required: true },
    disabled: { type: Boolean, default: false },
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
  computed: {
    camp() {
      return this.collaborator.camp()
    },
    isLastManager() {
      if (this.collaborator.status !== 'established') return false
      if (this.collaborator.role !== 'manager') return false
      const camp = this.collaborator.camp()
      return (
        camp
          ?.campCollaborations()
          ?.items?.filter((collaborator) => collaborator.status === 'established')
          .filter((collaborator) => collaborator.role === 'manager').length <= 1
      )
    },
    isOwnCampCollaboration() {
      if (!(typeof this.collaborator.user === 'function')) {
        return false
      }
      return this.$store.state.auth.user?.id === this.collaborator.user().id
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

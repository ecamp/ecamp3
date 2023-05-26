<template>
  <SettingsForm
    v-model="showDialog"
    :loading="loading"
    :error="error"
    icon="mdi-account-edit"
    :title="$tc('components.collaborator.collaboratorEdit.title', 2, { user: name })"
    :submit-action="update"
    :submit-label="$tc('global.button.save')"
    submit-color="success"
    :cancel-action="close"
  >
    <template #moreActions>
      <DialogEntityDelete v-if="inactive" :entity="collaborator">
        <template #activator="{ on }">
          <ButtonDelete class="v-btn--has-bg" :disabled="disabled" icon-first v-on="on" />
        </template>
        {{
          $tc('components.collaborator.collaboratorEdit.delete', 0, {
            name: name,
          })
        }}
        <br />
      </DialogEntityDelete>
      <IconButton
        v-if="collaborator.status === 'invited'"
        text
        class="v-btn--has-bg"
        color="blue-grey darken-2"
        icon-first
        :icon="resendingEmail ? 'mdi-refresh' : 'mdi-email-fast'"
        :animate="resendingEmail"
        :disabled="disabled || resendingEmail"
        @click="resendInvitation"
      >
        {{ $tc('components.collaborator.collaboratorEdit.resendEmail') }}
      </IconButton>
    </template>

    <template #activator="{ on }">
      <slot name="activator" v-bind="{ on }">
        <CollaboratorListItem
          :collaborator="collaborator"
          :disabled="!isManager"
          inactive
          v-on="on"
        />
      </slot>
    </template>

    <SettingsCollaboratorForm :collaboration="entityData" :status="collaborator.status">
      <template #statusChange>
        <v-tooltip
          v-if="collaborator.status !== 'inactive'"
          :disabled="disabled || !isLastManager"
          top
        >
          <template #activator="{ on, attrs }">
            <div v-bind="attrs" v-on="on">
              <DialogCollaboratorDeactivate :entity="collaborator">
                <template #activator="{ on: onDialog }">
                  <IconButton
                    color="secondary"
                    text
                    icon-first
                    :disabled="(disabled && !isOwnCampCollaboration) || isLastManager"
                    :icon-only="false"
                    icon="mdi-cancel"
                    v-on="onDialog"
                  >
                    {{ $tc('components.collaborator.collaboratorEdit.deactivate') }}
                  </IconButton>
                </template>
              </DialogCollaboratorDeactivate>
            </div>
          </template>
          <span>{{
            $tc('components.collaborator.collaboratorEdit.cannotRemoveLastManager')
          }}</span>
        </v-tooltip>
        <IconButton
          v-if="inactive"
          color="secondary"
          text
          icon-first
          icon="mdi-refresh"
          :animate="resendingEmail"
          :disabled="disabled || resendingEmail"
          @click="reinvite"
        >
          {{ $tc('components.collaborator.collaboratorEdit.inviteAgain') }}
        </IconButton>
      </template>
    </SettingsCollaboratorForm>
  </SettingsForm>
</template>

<script>
import DialogBase from '@/components/dialog/DialogBase.vue'
import SettingsCollaboratorForm from '@/components/collaborator/SettingsCollaboratorForm.vue'
import { campRoleMixin } from '@/mixins/campRoleMixin.js'
import IconButton from '@/components/buttons/IconButton.vue'
import SettingsForm from '@/components/dialog/SettingsForm.vue'
import ButtonDelete from '@/components/buttons/ButtonDelete.vue'
import DialogEntityDelete from '@/components/dialog/DialogEntityDelete.vue'
import DialogCollaboratorDeactivate from '@/components/collaborator/DialogCollaboratorDeactivate.vue'
import { errorToMultiLineToast } from '@/components/toast/toasts.js'
import CollaboratorListItem from '@/components/collaborator/CollaboratorListItem.vue'

export default {
  name: 'CollaboratorEdit',
  components: {
    CollaboratorListItem,
    DialogCollaboratorDeactivate,
    DialogEntityDelete,
    ButtonDelete,
    SettingsForm,
    IconButton,
    SettingsCollaboratorForm,
  },
  extends: DialogBase,
  mixins: [campRoleMixin],
  props: {
    collaborator: { type: Object, required: true },
    disabled: { type: Boolean, default: false },
    inactive: { type: Boolean, default: false },
  },
  data() {
    return {
      resendingEmail: false,
      entityProperties: ['camp', 'inviteEmail', 'role', 'status'],
      entityUri: '/camp_collaborations',
    }
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
    name() {
      return this.collaborator.user
        ? this.collaborator.user().displayName
        : this.collaborator.inviteEmail
    },
  },
  watch: {
    showDialog: function (showDialog) {
      if (showDialog) {
        this.entityUri = this.collaborator._meta.self
        this.setEntityData({
          role: this.collaborator.role,
        })
      } else {
        // clear form on exit
        this.clearEntityData()
      }
    },
  },
  methods: {
    resendInvitation() {
      this.resendingEmail = true
      this.api
        .href(this.api.get(), 'campCollaborations', {
          id: this.collaborator.id,
          action: 'resend_invitation',
        })
        .then((postUrl) => this.api.patch(postUrl, {}))
        .catch((e) => this.$toast.error(errorToMultiLineToast(e)))
        .finally(() => {
          this.resendingEmail = false
        })
    },
    reinvite() {
      this.resendingEmail = true
      this.api.patch(this.collaborator, { status: 'invited' }).then(() => {
        this.resendingEmail = false
      })
    },
  },
}
</script>

<style scoped></style>

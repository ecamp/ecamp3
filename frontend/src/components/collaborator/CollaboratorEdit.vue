<template>
  <DetailPane
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
      <PromptEntityDelete
        v-if="inactive"
        :entity="collaborator"
        align="left"
        position="top"
        :btn-attrs="{
          class: 'v-btn--has-bg',
          disabled,
        }"
      >
        {{
          $tc('components.collaborator.collaboratorEdit.delete', 0, {
            name: name,
          })
        }}
      </PromptEntityDelete>
      <IconButton
        v-if="collaborator.status === 'invited'"
        text
        class="v-btn--has-bg"
        color="blue-grey darken-2"
        :icon="
          resendingEmail
            ? 'mdi-refresh'
            : emailSent
              ? 'mdi-email-check'
              : 'mdi-email-fast'
        "
        :animate="resendingEmail"
        :disabled="disabled || resendingEmail || emailSent"
        @click="resendInvitation"
      >
        {{
          emailSent && !resendingEmail
            ? $tc('components.collaborator.collaboratorEdit.resentEmail')
            : $tc('components.collaborator.collaboratorEdit.resendEmail')
        }}
      </IconButton>
    </template>

    <template #activator="{ on }">
      <slot name="activator" v-bind="{ on }">
        <CollaboratorListItem
          :collaborator="collaborator"
          :disabled="!isManager"
          editable
          v-on="on"
        />
      </slot>
    </template>

    <CollaboratorForm
      :collaboration="entityData"
      :status="collaborator.status"
      :readonly-role="isLastManager"
      :initial-collaboration="collaborator"
    >
      <template #statusChange>
        <v-tooltip
          v-if="collaborator.status !== 'inactive'"
          :disabled="disabled || !isLastManager"
          top
          eager
        >
          <template #activator="{ on, attrs }">
            <div v-bind="attrs" v-on="on">
              <PromptCollaboratorDeactivate :entity="collaborator">
                <template #activator="{ on: onDialog, attrs: attrsDialog }">
                  <IconButton
                    color="secondary"
                    text
                    :aria-disabled="
                      (disabled && !isOwnCampCollaboration) || isLastManager
                    "
                    :icon-only="false"
                    icon="mdi-cancel"
                    v-bind="attrsDialog"
                    v-on="
                      (disabled && !isOwnCampCollaboration) || isLastManager
                        ? on
                        : onDialog
                    "
                  >
                    {{ $tc('components.collaborator.collaboratorEdit.deactivate') }}
                  </IconButton>
                </template>
              </PromptCollaboratorDeactivate>
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
          icon="mdi-refresh"
          :animate="resendingEmail"
          :disabled="disabled || resendingEmail"
          @click="reinvite"
        >
          {{ $tc('components.collaborator.collaboratorEdit.inviteAgain') }}
        </IconButton>
      </template>
    </CollaboratorForm>
  </DetailPane>
</template>

<script>
import DetailPane from '@/components/generic/DetailPane.vue'
import DialogBase from '@/components/dialog/DialogBase.vue'
import CollaboratorForm from '@/components/collaborator/CollaboratorForm.vue'
import { campRoleMixin } from '@/mixins/campRoleMixin.js'
import IconButton from '@/components/buttons/IconButton.vue'
import PromptCollaboratorDeactivate from '@/components/collaborator/PromptCollaboratorDeactivate.vue'
import { errorToMultiLineToast } from '@/components/toast/toasts.js'
import CollaboratorListItem from '@/components/collaborator/CollaboratorListItem.vue'
import PromptEntityDelete from '@/components/prompt/PromptEntityDelete.vue'
import campCollaborationDisplayName from '../../../../common/helpers/campCollaborationDisplayName'

export default {
  name: 'CollaboratorEdit',
  components: {
    PromptEntityDelete,
    CollaboratorListItem,
    DetailPane,
    CollaboratorForm,
    IconButton,
    PromptCollaboratorDeactivate,
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
      emailSent: false,
      entityProperties: ['abbreviation', 'color', 'role', 'status'],
      entityUri: '',
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
      return campCollaborationDisplayName(this.collaborator, this.$tc.bind(this), false)
    },
  },
  watch: {
    showDialog: function (showDialog) {
      if (showDialog) {
        this.emailSent = false
        this.entityUri = this.collaborator._meta.self
        this.setEntityData({
          abbreviation: this.collaborator.abbreviation,
          color: this.collaborator.color || '',
          role: this.collaborator.role,
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
    resendInvitation() {
      this.emailSent = true
      this.resendingEmail = true
      this.api
        .href(this.api.get(), 'campCollaborations', {
          id: this.collaborator.id,
          action: 'resend_invitation',
        })
        .then((postUrl) => this.api.patch(postUrl, {}))
        .catch((e) => {
          this.emailSent = false
          this.$toast.error(errorToMultiLineToast(e))
        })
        .finally(() => {
          this.resendingEmail = false
        })
    },
    reinvite() {
      this.resendingEmail = true
      this.api.patch(this.collaborator, { status: 'invited' }).finally(() => {
        this.resendingEmail = false
      })
    },
  },
}
</script>

<style scoped></style>

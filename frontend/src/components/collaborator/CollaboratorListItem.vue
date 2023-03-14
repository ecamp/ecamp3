<template>
  <v-list-item class="px-0 e-collaborator-item" two-line>
    <v-list-item-action>
      <user-avatar size="40" :camp-collaboration="collaborator" />
    </v-list-item-action>
    <v-list-item-content>
      <v-list-item-title>
        <span v-if="collaborator.user">{{ collaborator.user().displayName }}</span>
        <span v-else>{{ collaborator.inviteEmail }}</span>
      </v-list-item-title>
      <v-list-item-subtitle>
        {{ $tc(roles[collaborator.role].translation) }} &middot;
        <template v-for="icon in roles[collaborator.role].icons">
          <v-icon :key="icon" x-small>{{ icon }}</v-icon
          >&nbsp;
        </template>
      </v-list-item-subtitle>
    </v-list-item-content>
    <v-list-item-action v-if="inactive" class="e-collaborator-item__actions">
      <dialog-entity-delete :entity="collaborator">
        <template #activator="{ on }">
          <button-delete :disabled="disabled" v-on="on" />
        </template>
        {{
          $tc('components.collaborator.collaboratorListItem.delete', 0, {
            name: name,
          })
        }}
        <br />
      </dialog-entity-delete>
    </v-list-item-action>
    <v-list-item-action v-if="inactive" class="e-collaborator-item__actions ml-2">
      <icon-button
        color="primary"
        icon="mdi-refresh"
        :disabled="disabled"
        @click="api.patch(collaborator, { status: 'invited' })"
      >
        {{ $tc('components.collaborator.collaboratorListItem.inviteAgain') }}
      </icon-button>
    </v-list-item-action>
    <v-list-item-action v-if="!inactive" class="e-collaborator-item__actions ml-2">
      <icon-button
        v-if="collaborator.status === 'invited'"
        text
        color="blue-grey"
        :icon="resendingEmail ? 'mdi-refresh' : 'mdi-email-fast'"
        :hide-label="$vuetify.breakpoint.xsOnly"
        :animate="resendingEmail"
        :disabled="disabled || resendingEmail"
        @click="resendInvitation"
      >
        {{ $tc('components.collaborator.collaboratorListItem.resendEmail') }}
      </icon-button>
    </v-list-item-action>
    <v-list-item-action v-if="!inactive" class="e-collaborator-item__actions ml-2">
      <dialog-collaborator-edit :collaborator="collaborator">
        <template #activator="{ on }">
          <button-edit class="my-n1" v-on="on" />
        </template>
      </dialog-collaborator-edit>
    </v-list-item-action>
  </v-list-item>
</template>

<script>
import UserAvatar from '@/components/user/UserAvatar.vue'
import IconButton from '@/components/buttons/IconButton.vue'
import { errorToMultiLineToast } from '@/components/toast/toasts'
import ButtonEdit from '@/components/buttons/ButtonEdit.vue'
import DialogCollaboratorEdit from '@/components/collaborator/DialogCollaboratorEdit.vue'

export default {
  name: 'CollaboratorListItem',
  components: {
    DialogCollaboratorEdit,
    ButtonEdit,
    UserAvatar,
    IconButton,
  },
  props: {
    collaborator: { type: Object, required: true },
    disabled: { type: Boolean, default: false },
    inactive: { type: Boolean, default: false },
  },
  data: () => ({
    resendingEmail: false,
    roles: {
      manager: {
        translation: 'entity.camp.collaborators.manager',
        icons: ['mdi-eye', 'mdi-pencil', 'mdi-cog'],
      },
      member: {
        translation: 'entity.camp.collaborators.member',
        icons: ['mdi-eye', 'mdi-pencil'],
      },
      guest: {
        translation: 'entity.camp.collaborators.guest',
        icons: ['mdi-eye'],
      },
    },
  }),
  computed: {
    name() {
      return this.collaborator.user
        ? this.collaborator.user().displayName
        : this.collaborator.inviteEmail
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
  },
}
</script>

<style scoped></style>

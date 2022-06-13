<template>
  <v-list-item class="px-0" two-line>
    <v-list-item-avatar>
      <user-avatar :camp-collaboration="collaborator" />
    </v-list-item-avatar>
    <v-list-item-content>
      <v-list-item-title v-if="collaborator.user">
        {{ collaborator.user().displayName }}
      </v-list-item-title>
      <v-list-item-subtitle v-else>
        {{ collaborator.inviteEmail }}
      </v-list-item-subtitle>
    </v-list-item-content>
    <v-list-item-action v-if="collaborator.status === 'invited'" class="ml-2">
      <icon-button
        color="normal"
        icon="mdi-refresh"
        :animate="resendingEmail"
        :disabled="disabled"
        @click="resendInvitation"
      >
        {{ $tc('components.camp.collaboratorListItem.resendEmail') }}
      </icon-button>
    </v-list-item-action>
    <v-list-item-action class="ml-4">
      <v-tooltip :disabled="disabled || !isLastManager" top>
        <template #activator="{ on, attrs }">
          <div v-bind="attrs" v-on="on">
            <api-select
              :value="collaborator.role"
              :uri="collaborator._meta.self"
              fieldname="role"
              :items="[
                { key: 'member', translation: $tc('entity.camp.collaborators.member') },
                { key: 'manager', translation: $tc('entity.camp.collaborators.manager') },
                { key: 'guest', translation: $tc('entity.camp.collaborators.guest') },
              ]"
              item-value="key"
              item-text="translation"
              :my="0"
              dense
              vee-rules="required"
              :disabled="disabled || isLastManager"
            />
          </div>
        </template>
        <span>{{
          $tc('components.camp.collaboratorListItem.cannotAssignAnotherRoleToLastManager')
        }}</span>
      </v-tooltip>
    </v-list-item-action>
    <v-list-item-action class="ml-2">
      <v-tooltip :disabled="disabled || !isLastManager" top>
        <template #activator="{ on, attrs }">
          <div v-bind="attrs" v-on="on">
            <button-delete
              :disabled="(disabled && !isOwnCampCollaboration) || isLastManager"
              icon="mdi-cancel"
              @click="deactivateUser"
            >
              {{ $tc('components.camp.collaboratorListItem.deactivate') }}
            </button-delete>
          </div>
        </template>
        <span>{{
          $tc('components.camp.collaboratorListItem.cannotRemoveLastManager')
        }}</span>
      </v-tooltip>
    </v-list-item-action>
  </v-list-item>
</template>

<script>
import ApiSelect from '@/components/form/api/ApiSelect.vue'
import ButtonDelete from '@/components/buttons/ButtonDelete.vue'
import UserAvatar from '@/components/user/UserAvatar.vue'
import IconButton from '@/components/buttons/IconButton.vue'

export default {
  name: 'CollaboratorListItem',
  components: { ButtonDelete, ApiSelect, UserAvatar, IconButton },
  props: {
    collaborator: { type: Object, required: true },
    disabled: { type: Boolean, default: false },
  },
  data: () => ({
    resendingEmail: false,
  }),
  computed: {
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
      return this.$auth.user().id === this.collaborator.user().id
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
        .finally(() => {
          this.resendingEmail = false
        })
    },
    deactivateUser() {
      const ok = this.api.patch(this.collaborator, { status: 'inactive' })

      if (this.isOwnCampCollaboration) {
        // User left camp -> navigate to camp-overview
        ok.then(() => this.$router.push({ name: 'camps' }))
      }
    },
  },
}
</script>

<style scoped></style>

<template>
  <v-list-item class="px-2 rounded e-collaborator-item" two-line v-on="$listeners">
    <v-list-item-action>
      <user-avatar size="40" :camp-collaboration="collaborator" omit-sr />
    </v-list-item-action>
    <v-list-item-content>
      <v-list-item-title>
        <span v-if="collaborator.user">{{ collaborator.user().displayName }}</span>
        <span v-else>{{ collaborator.inviteEmail }}</span>
      </v-list-item-title>
      <v-list-item-subtitle>
        <v-tooltip right>
          <template #activator="{ on }">
            <button v-on="on">
              {{ $tc(roles[collaborator.role].roleTranslation)
              }}<span>
                &middot;
                <template v-for="icon in roles[collaborator.role].icons"
                  ><v-icon :key="icon" x-small>{{ icon }}</v-icon
                  >&thinsp;</template
                ></span
              >
            </button>
          </template>
          {{ $tc(roles[collaborator.role].abilitiesTranslation) }}
        </v-tooltip>
      </v-list-item-subtitle>
    </v-list-item-content>
    <v-list-item-action class="e-collaborator-item__actions ml-2">
      <button-edit icon-first color="primary--text" text class="my-n1 v-btn--has-bg" />
    </v-list-item-action>
  </v-list-item>
</template>

<script>
import UserAvatar from '@/components/user/UserAvatar.vue'
import ButtonEdit from '@/components/buttons/ButtonEdit.vue'

export default {
  name: 'CollaboratorListItem',
  components: {
    ButtonEdit,
    UserAvatar,
  },
  props: {
    collaborator: { type: Object, required: true },
    disabled: { type: Boolean, default: false },
    inactive: { type: Boolean, default: false },
  },
  data: () => ({
    roles: {
      manager: {
        roleTranslation: 'entity.camp.collaborators.manager',
        abilitiesTranslation: 'entity.camp.collaborators.managerAbilities',
        icons: ['mdi-eye-outline', 'mdi-pencil-outline', 'mdi-cog-outline'],
      },
      member: {
        roleTranslation: 'entity.camp.collaborators.member',
        abilitiesTranslation: 'entity.camp.collaborators.memberAbilities',
        icons: ['mdi-eye-outline', 'mdi-pencil-outline'],
      },
      guest: {
        roleTranslation: 'entity.camp.collaborators.guest',
        abilitiesTranslation: 'entity.camp.collaborators.guestAbilities',
        icons: ['mdi-eye-outline'],
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
}
</script>

<style scoped>
.v-list-item--link:before {
  border-radius: 6px;
}
</style>
